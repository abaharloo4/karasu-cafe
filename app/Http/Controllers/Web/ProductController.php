<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $categoryId = $request->query('category_id');

        $categories = Category::where('cafe_id', $cafeId)->where('status', 'active')->get();

        $query = Product::where('cafe_id', $cafeId)->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->latest()->get();

        return view('products.index', compact('products', 'categories', 'categoryId'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);

        $cafeId = auth()->user()->cafe_id ?? 1;
        $categories = Category::where('cafe_id', $cafeId)->where('status', 'active')->get();

        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Product::class);

        $cafeId = auth()->user()->cafe_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'prep_time' => 'nullable|integer|min:1|max:120',
            'tasting_notes' => 'nullable|string|max:255',
            'bean_type' => 'nullable|string|max:255',
            'serving_type' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:4096',
            'stock' => 'nullable|integer|min:0',
            'addons' => 'nullable',
            'discount_percent' => 'nullable|integer|min:0|max:99',
            'discount_start_time' => 'nullable|string|max:5',
            'discount_end_time' => 'nullable|string|max:5',
            'discount_days' => 'nullable|array',
            'discount_days.*' => 'string|in:sat,sun,mon,tue,wed,thu,fri',
            'is_hourly_discount_active' => 'nullable|boolean',
        ]);

        $imagePath = $validated['image'] ?? null;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $uploaded = $file->store('products', 'public');
            $imagePath = '/storage/' . $uploaded;

            // Mirror to public/storage if not a symlink
            $publicDest = public_path('storage/' . $uploaded);
            if (!is_link(public_path('storage'))) {
                @mkdir(dirname($publicDest), 0755, true);
                @copy(storage_path('app/public/' . $uploaded), $publicDest);
            }

            \App\Models\MediaItem::firstOrCreate(
                ['path' => $uploaded],
                [
                    'cafe_id' => $cafeId,
                    'filename' => $file->getClientOriginalName(),
                    'folder' => 'products',
                    'disk' => 'public',
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'alt_text' => $validated['name'],
                    'title' => $validated['name'],
                ]
            );
        }

        // Parse and clean addons
        $addons = $this->parseAddons($request->input('addons'));

        // Use category name as type for backward compatibility
        $category = Category::find($validated['category_id']);

        Product::create([
            'cafe_id' => $cafeId,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'type' => $category ? $category->name : 'other',
            'price' => $validated['price'],
            'stock' => $request->filled('stock') ? (int) $request->input('stock') : null,
            'description' => $validated['description'] ?? null,
            'prep_time' => $validated['prep_time'] ?? 15,
            'tasting_notes' => $validated['tasting_notes'] ?? null,
            'bean_type' => $validated['bean_type'] ?? null,
            'serving_type' => $validated['serving_type'] ?? null,
            'addons' => !empty($addons) ? $addons : null,
            'image' => $imagePath,
            'status' => 'active',
            'discount_percent' => (int) ($validated['discount_percent'] ?? 0),
            'discount_start_time' => $validated['discount_start_time'] ?? null,
            'discount_end_time' => $validated['discount_end_time'] ?? null,
            'discount_days' => $request->input('discount_days'),
            'is_hourly_discount_active' => $request->boolean('is_hourly_discount_active'),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'محصول جدید به منو اضافه شد.');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);

        $cafeId = auth()->user()->cafe_id ?? 1;
        $categories = Category::where('cafe_id', $cafeId)->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('update', $product);

        $cafeId = $product->cafe_id ?? (auth()->user()->cafe_id ?? 1);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'prep_time' => 'nullable|integer|min:1|max:120',
            'tasting_notes' => 'nullable|string|max:255',
            'bean_type' => 'nullable|string|max:255',
            'serving_type' => 'nullable|string|max:255',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,webp,jpg|max:4096',
            'remove_image' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
            'stock' => 'nullable|integer|min:0',
            'addons' => 'nullable',
            'discount_percent' => 'nullable|integer|min:0|max:99',
            'discount_start_time' => 'nullable|string|max:5',
            'discount_end_time' => 'nullable|string|max:5',
            'discount_days' => 'nullable|array',
            'discount_days.*' => 'string|in:sat,sun,mon,tue,wed,thu,fri',
            'is_hourly_discount_active' => 'nullable|boolean',
        ]);

        $imagePath = $product->getRawOriginal('image');

        if ($request->boolean('remove_image')) {
            // Delete physical file from disk if it was stored in products/
            if ($imagePath) {
                $cleanCurrent = ltrim(str_replace(['/storage/', 'storage/'], '', $imagePath), '/\\');
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanCurrent)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanCurrent);
                }
                $oldPub = public_path('storage/' . $cleanCurrent);
                if (file_exists($oldPub) && is_file($oldPub)) {
                    @unlink($oldPub);
                }
                \App\Models\MediaItem::where('path', $cleanCurrent)->delete();
            }
            $imagePath = null;
        } elseif ($request->hasFile('image_file')) {
            // Delete old file from storage when replacing
            if ($imagePath) {
                $cleanCurrent = ltrim(str_replace(['/storage/', 'storage/'], '', $imagePath), '/\\');
                if (str_starts_with($cleanCurrent, 'products/')) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanCurrent)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanCurrent);
                    }
                    $oldPub = public_path('storage/' . $cleanCurrent);
                    if (file_exists($oldPub) && is_file($oldPub)) {
                        @unlink($oldPub);
                    }
                    \App\Models\MediaItem::where('path', $cleanCurrent)->delete();
                }
            }

            $file = $request->file('image_file');
            $uploaded = $file->store('products', 'public');
            $imagePath = '/storage/' . $uploaded;

            // Mirror to public/storage if not a symlink
            $publicDest = public_path('storage/' . $uploaded);
            if (!is_link(public_path('storage'))) {
                @mkdir(dirname($publicDest), 0755, true);
                @copy(storage_path('app/public/' . $uploaded), $publicDest);
            }

            \App\Models\MediaItem::firstOrCreate(
                ['path' => $uploaded],
                [
                    'cafe_id' => $cafeId,
                    'filename' => $file->getClientOriginalName(),
                    'folder' => 'products',
                    'disk' => 'public',
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'alt_text' => $validated['name'],
                    'title' => $validated['name'],
                ]
            );
        } elseif ($request->has('image')) {
            $valImage = trim($request->input('image', '') ?? '');
            $imagePath = !empty($valImage) ? $valImage : null;
        }

        $addons = $this->parseAddons($request->input('addons'));

        // Use category name as type for backward compatibility
        $category = Category::find($validated['category_id']);

        $product->update([
            'name' => $validated['name'],
            'category_id' => $validated['category_id'],
            'type' => $category ? $category->name : 'other',
            'price' => $validated['price'],
            'stock' => $request->filled('stock') ? (int) $request->input('stock') : null,
            'description' => $validated['description'] ?? null,
            'prep_time' => $validated['prep_time'] ?? 15,
            'tasting_notes' => $validated['tasting_notes'] ?? null,
            'bean_type' => $validated['bean_type'] ?? null,
            'serving_type' => $validated['serving_type'] ?? null,
            'addons' => !empty($addons) ? $addons : null,
            'image' => $imagePath,
            'status' => $validated['status'],
            'discount_percent' => (int) ($validated['discount_percent'] ?? 0),
            'discount_start_time' => $validated['discount_start_time'] ?? null,
            'discount_end_time' => $validated['discount_end_time'] ?? null,
            'discount_days' => $request->input('discount_days'),
            'is_hourly_discount_active' => $request->boolean('is_hourly_discount_active'),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'اطلاعات محصول با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        $rawImage = $product->getRawOriginal('image');
        if ($rawImage) {
            $cleanPath = ltrim(str_replace(['/storage/', 'storage/'], '', $rawImage), '/\\');
            if (str_starts_with($cleanPath, 'products/')) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($cleanPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanPath);
                }
                $pubFile = public_path('storage/' . $cleanPath);
                if (file_exists($pubFile) && is_file($pubFile)) {
                    @unlink($pubFile);
                }
                \App\Models\MediaItem::where('path', $cleanPath)->delete();
            }
        }

        $product->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'محصول با موفقیت از سیستم حذف شد.',
            ]);
        }

        return redirect()->route('products.index')
            ->with('success', 'محصول با موفقیت از سیستم حذف شد.');
    }

    protected function parseAddons($rawAddons): array
    {
        if (empty($rawAddons)) {
            return [];
        }

        if (is_string($rawAddons)) {
            $decoded = json_decode($rawAddons, true);
            if (is_array($decoded)) {
                $rawAddons = $decoded;
            }
        }

        $addons = [];
        if (is_array($rawAddons)) {
            foreach ($rawAddons as $item) {
                if (is_array($item) && !empty($item['name'])) {
                    $addons[] = [
                        'name' => trim(strip_tags($item['name'])),
                        'price' => max(0, (float)($item['price'] ?? 0)),
                    ];
                }
            }
        }

        return $addons;
    }
}
