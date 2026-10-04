<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DeliveryZone;
use Illuminate\Http\Request;

class DeliveryZoneController extends Controller
{
    public function index()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $zones = DeliveryZone::where('cafe_id', $cafeId)->latest()->get();

        return view('admin.delivery-zones.index', compact('zones'));
    }

    public function store(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'delivery_price' => 'required|numeric|min:0',
            'min_order_free_delivery' => 'nullable|numeric|min:0',
            'center_lat' => 'nullable|numeric',
            'center_lng' => 'nullable|numeric',
            'radius_km' => 'nullable|numeric|min:0.1|max:50',
            'boundary' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $boundary = $request->input('boundary');
        if (is_string($boundary)) {
            $boundary = json_decode($boundary, true);
        }

        DeliveryZone::create([
            'cafe_id' => $cafeId,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'delivery_price' => $validated['delivery_price'],
            'min_order_free_delivery' => $validated['min_order_free_delivery'] ?? 0,
            'center_lat' => $validated['center_lat'] ?? 29.6293,
            'center_lng' => $validated['center_lng'] ?? 52.5186,
            'radius_km' => $validated['radius_km'] ?? 5.0,
            'boundary' => $boundary,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'منطقه ارسال جدید در شیراز با موفقیت ثبت شد.');
    }

    public function update(Request $request, DeliveryZone $deliveryZone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'delivery_price' => 'required|numeric|min:0',
            'min_order_free_delivery' => 'nullable|numeric|min:0',
            'center_lat' => 'nullable|numeric',
            'center_lng' => 'nullable|numeric',
            'radius_km' => 'nullable|numeric|min:0.1|max:50',
            'boundary' => 'nullable',
            'status' => 'required|in:active,inactive',
        ]);

        $boundary = $request->input('boundary');
        if (is_string($boundary)) {
            $boundary = json_decode($boundary, true);
        }

        $deliveryZone->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'delivery_price' => $validated['delivery_price'],
            'min_order_free_delivery' => $validated['min_order_free_delivery'] ?? 0,
            'center_lat' => $validated['center_lat'] ?? $deliveryZone->center_lat,
            'center_lng' => $validated['center_lng'] ?? $deliveryZone->center_lng,
            'radius_km' => $validated['radius_km'] ?? $deliveryZone->radius_km,
            'boundary' => $boundary ?? $deliveryZone->boundary,
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'اطلاعات منطقه ارسال با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(DeliveryZone $deliveryZone)
    {
        $deliveryZone->delete();

        return redirect()->route('admin.delivery-zones.index')
            ->with('success', 'منطقه ارسال حذف شد.');
    }
}
