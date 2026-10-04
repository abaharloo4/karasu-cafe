<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $categories = Category::where('cafe_id', $cafeId)->withCount('products')->latest()->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon_svg' => 'nullable|string',
            'icon_file' => 'nullable|file|mimes:svg,xml|max:1024',
        ]);

        $svgCode = $validated['icon_svg'] ?? null;
        if ($request->hasFile('icon_file')) {
            $svgCode = file_get_contents($request->file('icon_file')->getRealPath());
        }

        $cleanSvg = $this->sanitizeSvg($svgCode);

        Category::create([
            'cafe_id' => $cafeId,
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'], '-', null),
            'icon_svg' => $cleanSvg,
            'status' => 'active',
        ]);

        return redirect()->back()->with('success', 'دسته‌بندی جدید با موفقیت ایجاد شد.');
    }

    public function update(Request $request, Category $category)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        abort_if($category->cafe_id !== $cafeId, 403, 'دسترسی غیرمجاز.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'icon_svg' => 'nullable|string',
            'icon_file' => 'nullable|file|mimes:svg,xml|max:1024',
        ]);

        $svgCode = $category->icon_svg;
        if ($request->has('icon_svg')) {
            $svgCode = $validated['icon_svg'];
        }
        if ($request->hasFile('icon_file')) {
            $svgCode = file_get_contents($request->file('icon_file')->getRealPath());
        }

        $cleanSvg = $this->sanitizeSvg($svgCode);

        $category->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name'], '-', null),
            'icon_svg' => $cleanSvg,
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'دسته‌بندی با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Category $category)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        abort_if($category->cafe_id !== $cafeId, 403, 'دسترسی غیرمجاز.');

        $category->delete();

        return redirect()->back()->with('success', 'دسته‌بندی حذف شد.');
    }

    /**
     * Sanitize SVG markup to prevent Stored XSS attacks.
     */
    protected function sanitizeSvg(?string $svg): ?string
    {
        if (empty($svg)) {
            return null;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        if (!$doc->loadXML(trim($svg), LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING)) {
            libxml_clear_errors();
            return null;
        }
        libxml_clear_errors();

        $allowedTags = ['svg', 'path', 'g', 'circle', 'rect', 'line', 'polyline', 'polygon', 'ellipse'];
        $allowedAttributes = [
            'viewbox', 'fill', 'stroke', 'stroke-width', 'stroke-linecap',
            'stroke-linejoin', 'd', 'cx', 'cy', 'r', 'rx', 'ry', 'x', 'y',
            'width', 'height', 'xmlns', 'class', 'transform', 'opacity'
        ];

        $xpath = new \DOMXPath($doc);
        $nodes = $xpath->query('//*');

        foreach ($nodes as $node) {
            if (!in_array(strtolower($node->nodeName), $allowedTags, true)) {
                $node->parentNode?->removeChild($node);
                continue;
            }

            if ($node->hasAttributes()) {
                $attrsToRemove = [];
                foreach ($node->attributes as $attr) {
                    $attrName = strtolower($attr->nodeName);
                    $attrValue = strtolower($attr->nodeValue);
                    if (
                        !in_array($attrName, $allowedAttributes, true) ||
                        str_starts_with($attrName, 'on') ||
                        str_contains($attrValue, 'javascript:') ||
                        str_contains($attrValue, 'data:')
                    ) {
                        $attrsToRemove[] = $attr->nodeName;
                    }
                }
                foreach ($attrsToRemove as $attrName) {
                    $node->removeAttribute($attrName);
                }
            }
        }

        return $doc->documentElement ? $doc->saveXML($doc->documentElement) : null;
    }
}
