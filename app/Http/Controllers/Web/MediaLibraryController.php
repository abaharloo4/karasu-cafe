<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    /**
     * Display the media library items, syncing with physical storage directories.
     */
    public function index()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        // Ensure directories exist
        Storage::disk('public')->makeDirectory('media');
        Storage::disk('public')->makeDirectory('products');

        // Automatically prune MediaItems whose files were deleted from disk
        MediaItem::where('cafe_id', $cafeId)->chunk(100, function ($items) {
            foreach ($items as $item) {
                $cleanPath = str_replace('\\', '/', $item->path);
                if (!Storage::disk('public')->exists($cleanPath)) {
                    $item->delete();
                }
            }
        });

        // Automatically sync physical files from disk to DB if not already indexed
        $directories = ['media', 'products'];
        foreach ($directories as $dir) {
            $files = Storage::disk('public')->files($dir);
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                    $cleanPath = str_replace('\\', '/', $file);
                    MediaItem::firstOrCreate(
                        ['path' => $cleanPath],
                        [
                            'cafe_id' => $cafeId,
                            'filename' => basename($cleanPath),
                            'title' => pathinfo($cleanPath, PATHINFO_FILENAME),
                            'alt_text' => 'تصویر ' . pathinfo($cleanPath, PATHINFO_FILENAME),
                            'folder' => $dir,
                            'disk' => 'public',
                            'size' => Storage::disk('public')->size($cleanPath),
                            'mime_type' => Storage::disk('public')->mimeType($cleanPath) ?? ('image/' . $ext),
                        ]
                    );
                }
            }
        }

        $mediaItems = MediaItem::where('cafe_id', $cafeId)
            ->latest()
            ->paginate(50);

        $mediaFiles = $mediaItems->map(fn($item) => [
            'id' => $item->id,
            'name' => $item->filename,
            'path' => $item->path,
            'url' => $item->url,
            'relative_url' => $item->relative_url,
            'size' => $item->size,
            'formatted_size' => $item->formatted_size,
            'title' => $item->title,
            'alt_text' => $item->alt_text,
            'disk' => $item->disk,
            'is_product' => $item->folder === 'products',
            'created_at' => $item->created_at?->format('Y-m-d H:i'),
        ]);

        return view('admin.media.index', compact('mediaItems', 'mediaFiles'));
    }

    /**
     * Store new images in the media library with optional Persian title and alt text.
     */
    public function store(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpeg,png,webp,jpg,gif,svg|max:5120',
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ]);

        $created = [];
        $defaultAlt = $request->input('alt_text');
        $defaultTitle = $request->input('title');

        foreach ($request->file('files') as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $cleanSlug = Str::slug($originalName) ?: 'image';
            $filename = time() . '_' . $cleanSlug . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('media', $filename, 'public');
            $cleanPath = str_replace('\\', '/', $path);

            // Mirror to public/storage if not a symlink
            $publicDest = public_path('storage/' . $cleanPath);
            if (!is_link(public_path('storage'))) {
                @mkdir(dirname($publicDest), 0755, true);
                @copy(storage_path('app/public/' . $cleanPath), $publicDest);
            }

            $mediaItem = MediaItem::create([
                'cafe_id' => $cafeId,
                'filename' => $filename,
                'path' => $cleanPath,
                'disk' => 'public',
                'title' => $defaultTitle ?: $originalName,
                'alt_text' => $defaultAlt ?: $originalName,
                'folder' => 'media',
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ]);

            $created[] = $mediaItem;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'items' => $created,
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'تعداد ' . count($created) . ' تصویر با موفقیت ذخیره شد.');
    }

    /**
     * Update Alt text and Title of a media item.
     */
    public function update(Request $request, MediaItem $mediaItem)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        abort_if($mediaItem->cafe_id !== $cafeId, 403);

        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:255',
        ]);

        $mediaItem->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'متن جایگزین (Alt) و عنوان تصویر با موفقیت به‌روزرسانی شد.',
                'item' => $mediaItem,
            ]);
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'متن جایگزین (Alt) و مشخصات تصویر به‌روزرسانی شد.');
    }

    /**
     * Delete an image from storage and database.
     */
    public function destroy(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $mediaId = $request->input('id');
        $rawPath = $request->input('path');

        $mediaItem = null;
        if ($mediaId) {
            $mediaItem = MediaItem::where('cafe_id', $cafeId)->find($mediaId);
        }

        if (!$mediaItem && $rawPath) {
            $cleanPath = ltrim(str_replace(['../', '..\\', '\\'], ['', '', '/'], $rawPath), '/');
            // Strip /storage/ prefix if present
            if (Str::startsWith($cleanPath, 'storage/')) {
                $cleanPath = Str::after($cleanPath, 'storage/');
            }
            $mediaItem = MediaItem::where('cafe_id', $cafeId)->where('path', $cleanPath)->first();
        }

        if ($mediaItem) {
            $cleanPath = str_replace('\\', '/', $mediaItem->path);
            if (Storage::disk('public')->exists($cleanPath)) {
                Storage::disk('public')->delete($cleanPath);
            }
            $pubFile = public_path('storage/' . $cleanPath);
            if (file_exists($pubFile) && is_file($pubFile)) {
                @unlink($pubFile);
            }
            $mediaItem->delete();

            // Clear this image from any products referencing it
            $baseName = basename($cleanPath);
            \App\Models\Product::where('cafe_id', $cafeId)
                ->where(function($q) use ($cleanPath, $baseName) {
                    $q->where('image', 'LIKE', '%' . $baseName)
                      ->orWhere('image', $cleanPath)
                      ->orWhere('image', '/storage/' . $cleanPath)
                      ->orWhere('image', 'storage/' . $cleanPath);
                })
                ->update(['image' => null]);

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'تصویر با موفقیت حذف شد.']);
            }

            return redirect()->route('admin.media.index')
                ->with('success', 'تصویر با موفقیت از کتابخانه رسانه و دیسک سرور حذف شد.');
        }

        // Direct path fallback for unindexed files
        if ($rawPath) {
            $cleanPath = ltrim(str_replace(['../', '..\\', '\\'], ['', '', '/'], $rawPath), '/');
            if (Str::startsWith($cleanPath, 'storage/')) {
                $cleanPath = Str::after($cleanPath, 'storage/');
            }

            // Allow deleting from both media and products folders
            if (Str::startsWith($cleanPath, 'media/') || Str::startsWith($cleanPath, 'products/')) {
                if (Storage::disk('public')->exists($cleanPath)) {
                    Storage::disk('public')->delete($cleanPath);

                    $baseName = basename($cleanPath);
                    \App\Models\Product::where('cafe_id', $cafeId)
                        ->where(function($q) use ($cleanPath, $baseName) {
                            $q->where('image', 'LIKE', '%' . $baseName)
                              ->orWhere('image', $cleanPath)
                              ->orWhere('image', '/storage/' . $cleanPath)
                              ->orWhere('image', 'storage/' . $cleanPath);
                        })
                        ->update(['image' => null]);

                    return redirect()->route('admin.media.index')
                        ->with('success', 'تصویر با موفقیت از سرور حذف گردید.');
                }
            }
        }

        return redirect()->route('admin.media.index')
            ->with('error', 'تصویر مورد نظر برای حذف یافت نشد.');
    }

    /**
     * JSON listing for product picker modal.
     */
    public function listJson(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $items = MediaItem::where('cafe_id', $cafeId)
            ->latest()
            ->take(50)
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'name' => $item->filename,
                'title' => $item->title ?? $item->filename,
                'alt_text' => $item->alt_text ?? '',
                'url' => $item->url,
                'relative_url' => $item->relative_url,
                'formatted_size' => $item->formatted_size,
            ]);

        return response()->json(['items' => $items]);
    }
}
