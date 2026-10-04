@extends('layouts.app')

@section('title', 'مدیریت مناطق ارسال شیراز (نقشه)')
@section('header_title', 'مناطق ارسال با پیک در شیراز - بی کافه (B Cafe)')

@section('content')
<!-- Leaflet & Leaflet Draw for Polygon Drawing -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js" crossorigin=""></script>

<div class="space-y-6" x-data="{
    editModal: false,
    editId: null,
    editName: '',
    editDesc: '',
    editPrice: 0,
    editMinFree: 0,
    editRadius: 5,
    editStatus: 'active',
    editBoundary: '',
    newBoundary: '',
    pointCount: 0
}">

    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-black text-[#353535] flex items-center gap-2">
                <span class="w-2.5 h-5 rounded-full bg-[#284B63]"></span>
                <span>مدیریت و رسم محدوده‌های چندضلعی ارسال با پیک (شیراز)</span>
            </h2>
            <p class="text-xs text-[#353535]/65 mt-1">محدوده‌های تحت پوشش پیک را مستقیماً روی نقشه شیراز رسم کنید تا بررسی سفارشات به صورت سروری و Point-in-Polygon انجام شود.</p>
        </div>

        <a href="{{ route('dashboard') }}" class="px-4 py-2.5 bg-white border border-[#353535]/15 hover:bg-[#F7F9FA] text-[#353535] font-bold rounded-xl text-xs transition-all flex items-center gap-2 shadow-2xs">
            <svg class="w-4 h-4 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            <span>داشبورد</span>
        </a>
    </div>

    <!-- Map Showcase & Drawing Canvas -->
    <div class="bg-white border border-[#353535]/12 rounded-3xl p-5 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-[#284B63]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                <h3 class="text-sm font-black text-[#353535]">نقشه تعاملی مناطق شیراز (Neshan / Leaflet Polygon Drawing)</h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-[#353535]/70" x-show="pointCount > 0">
                    نقاط رسم‌شده محدوده جدید: <span class="font-bold text-[#284B63]" x-text="pointCount"></span> نقطه
                </span>
                <button type="button" id="clear-drawn-btn" class="px-3 py-1.5 bg-[#F7F9FA] hover:bg-[#284B63] hover:text-white text-[#353535] rounded-xl text-xs font-bold transition-colors">
                    پاکسازی رسم جدید
                </button>
            </div>
        </div>

        <div id="admin-zone-map" class="w-full h-80 sm:h-96 rounded-2xl overflow-hidden border border-[#353535]/15 z-10"></div>
        <p class="text-[11px] text-[#353535]/60">راهنما: با استفاده از ابزار چندضلعی (Polygon) در سمت چپ نقشه، گوشه‌های محدوده را در شیراز مشخص کنید و با دو بار کلیک، چندضلعی را ببندید تا مختصات در فرم زیر ثبت شود.</p>
    </div>

    <!-- Create Card & Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Add Delivery Zone Form -->
        <div class="bg-white border border-[#353535]/12 p-6 rounded-3xl space-y-4 shadow-xs h-fit">
            <h3 class="text-sm font-black text-[#353535] border-b border-[#353535]/10 pb-3 flex items-center gap-2">
                <svg class="w-4 h-4 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                <span>تعریف منطقه ارسال جدید</span>
            </h3>

            <form method="POST" action="{{ route('admin.delivery-zones.store') }}" class="space-y-4" id="create-zone-form">
                @csrf
                <input type="hidden" name="boundary" id="create-boundary-input" x-model="newBoundary">

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">نام منطقه / محله شیراز *</label>
                    <input type="text" name="name" placeholder="مثلاً: منطقه ۱ - ارم، قصرالدشت، عفیف‌آباد" required class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:bg-white">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1.5">توضیحات و خیابان‌های تحت پوشش</label>
                    <input type="text" name="description" placeholder="خیابان‌های اصلی یا محدوده‌های تحت پوشش این منطقه..." class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-medium focus:outline-none focus:border-[#3C6E71] focus:bg-white">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1.5">هزینه پیک (تومان) *</label>
                        <input type="number" name="delivery_price" placeholder="30000" required class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#284B63] text-xs font-black tabular-nums focus:outline-none focus:border-[#3C6E71] focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1.5">ارسال رایگان از مبلغ</label>
                        <input type="number" name="min_order_free_delivery" value="0" placeholder="0 = غیرفعال" class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#3C6E71] text-xs font-bold tabular-nums focus:outline-none focus:border-[#3C6E71] focus:bg-white">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1.5">شعاع fallback (کیلومتر)</label>
                        <input type="number" step="0.5" name="radius_km" value="5.0" class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold tabular-nums focus:outline-none focus:border-[#3C6E71] focus:bg-white">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1.5">وضعیت سرویس‌دهی *</label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71] focus:bg-white">
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                        </select>
                    </div>
                </div>

                <div class="p-3 bg-[#F7F9FA]/50 rounded-xl border border-[#353535]/10 text-[11px] text-[#353535]/80">
                    <span class="font-bold block mb-1">وضعیت محدوده پلی‌گون:</span>
                    <span x-show="!newBoundary" class="text-[#284B63]">هنوز روی نقشه رسم نشده است (از شعاع پیش‌فرض استفاده می‌شود).</span>
                    <span x-show="newBoundary" class="text-[#3C6E71] font-bold">چندضلعی با موفقیت روی نقشه شیراز ثبت شد.</span>
                </div>

                <button type="submit" class="w-full py-3 bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] font-black rounded-xl text-xs transition-all shadow-md flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>ثبت منطقه ارسال</span>
                </button>
            </form>
        </div>

        <!-- Zones List Table -->
        <div class="lg:col-span-2 bg-white border border-[#353535]/12 rounded-3xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-start text-xs">
                    <thead>
                        <tr class="bg-[#F7F9FA]/60 text-[#353535]/70 border-b border-[#353535]/10">
                            <th class="p-4 font-black">نام منطقه</th>
                            <th class="p-4 font-black">هزینه پیک</th>
                            <th class="p-4 font-black">نوع محدوده</th>
                            <th class="p-4 font-black">وضعیت</th>
                            <th class="p-4 font-black text-center">عملیات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#353535]/8 text-[#353535]">
                        @forelse($zones as $zone)
                            <tr class="hover:bg-[#F7F9FA]/25 transition-colors">
                                <td class="p-4">
                                    <span class="font-black text-[#353535] block">{{ $zone->name }}</span>
                                    <span class="text-[11px] text-[#353535]/60">{{ $zone->description ?? 'بدون توضیحات' }}</span>
                                </td>
                                <td class="p-4 font-black text-[#284B63] tabular-nums">{{ number_format($zone->delivery_price) }} تومان</td>
                                <td class="p-4">
                                    @if(!empty($zone->boundary) || !empty($zone->polygon_coordinates))
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-[#3C6E71]/15 text-[#353535] text-[11px] font-bold">
                                            <svg class="w-3 h-3 text-[#3C6E71]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                                            <span>پلی‌گون نقشه</span>
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-lg bg-[#F7F9FA] text-[#353535]/70 text-[11px] font-bold">شعاع تقریبی ({{ $zone->radius_km }}km)</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $zone->status === 'active' ? 'bg-[#3C6E71]/15 text-[#353535]' : 'bg-[#B44A4A]/10 text-[#B44A4A]' }}">
                                        {{ $zone->status === 'active' ? 'فعال' : 'غیرفعال' }}
                                    </span>
                                </td>
                                <td class="p-4 text-center space-x-2 space-x-reverse">
                                    <button type="button"
                                            @click="editModal = true;
                                                    editId = {{ $zone->id }};
                                                    editName = '{{ addslashes($zone->name) }}';
                                                    editDesc = '{{ addslashes($zone->description ?? '') }}';
                                                    editPrice = {{ (int)$zone->delivery_price }};
                                                    editMinFree = {{ (int)$zone->min_order_free_delivery }};
                                                    editRadius = {{ (float)$zone->radius_km }};
                                                    editStatus = '{{ $zone->status }}';
                                                    editBoundary = '{{ json_encode($zone->boundary ?? $zone->polygon_coordinates) }}'"
                                            class="px-3 py-1.5 bg-[#F7F9FA] hover:bg-[#3C6E71] hover:text-[#F7F9FA] text-[#353535] border border-[#353535]/10 rounded-xl text-[11px] font-bold transition-all">
                                        ویرایش
                                    </button>
                                    <form method="POST" action="{{ route('admin.delivery-zones.destroy', $zone) }}" class="inline-block" onsubmit="return confirm('آیا از حذف این منطقه ارسال اطمینان دارید؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-white hover:bg-[#B44A4A] hover:text-white text-[#B44A4A] border border-[#B44A4A]/20 rounded-xl text-[11px] font-bold transition-all">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-[#353535]/60">هنوز منطقه ارسالی تعریف نشده است.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Edit Delivery Zone Modal -->
    <div x-show="editModal" class="fixed inset-0 bg-black/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="bg-white border border-[#353535]/15 rounded-3xl p-6 w-full max-w-lg space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-[#353535]/10 pb-3">
                <h3 class="text-sm font-black text-[#353535]">ویرایش منطقه ارسال شیراز</h3>
                <button type="button" @click="editModal = false" class="w-8 h-8 rounded-lg bg-[#F7F9FA] hover:bg-[#B44A4A]/10 text-[#353535] hover:text-[#B44A4A] flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('/admin/delivery-zones') }}/' + editId" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="boundary" x-model="editBoundary">

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1">نام منطقه *</label>
                    <input type="text" name="name" x-model="editName" required class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-[#353535] mb-1">توضیحات</label>
                    <input type="text" name="description" x-model="editDesc" class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-medium focus:outline-none focus:border-[#3C6E71]">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">هزینه پیک (تومان) *</label>
                        <input type="number" name="delivery_price" x-model="editPrice" required class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#284B63] text-xs font-black tabular-nums focus:outline-none focus:border-[#3C6E71]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">حداقل ارسال رایگان (تومان)</label>
                        <input type="number" name="min_order_free_delivery" x-model="editMinFree" class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#3C6E71] text-xs font-bold tabular-nums focus:outline-none focus:border-[#3C6E71]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">شعاع (کیلومتر)</label>
                        <input type="number" step="0.5" name="radius_km" x-model="editRadius" class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold tabular-nums focus:outline-none focus:border-[#3C6E71]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[#353535] mb-1">وضعیت *</label>
                        <select name="status" x-model="editStatus" class="w-full px-4 py-2.5 rounded-xl bg-[#F7F9FA]/40 border border-[#353535]/15 text-[#353535] text-xs font-bold focus:outline-none focus:border-[#3C6E71]">
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-[#353535]/10">
                    <button type="button" @click="editModal = false" class="px-4 py-2.5 bg-[#F7F9FA] text-[#353535] font-bold rounded-xl text-xs">انصراف</button>
                    <button type="submit" class="px-5 py-2.5 bg-[#353535] hover:bg-[#1E2912] text-[#F7F9FA] font-black rounded-xl text-xs shadow-md">به‌روزرسانی منطقه</button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Initialize Shiraz Interactive Polygon Map -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const shirazCenter = [29.6293, 52.5186];
    const map = L.map('admin-zone-map').setView(shirazCenter, 13);

    // Neshan/OpenStreetMap Standard Tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© B Cafe Shiraz | Neshan Maps Engine'
    }).addTo(map);

    // FeatureGroup to store editable layers
    const drawnItems = new L.FeatureGroup();
    map.addLayer(drawnItems);

    // Render existing zones on map
    const existingZones = @json($zones);
    existingZones.forEach(zone => {
        const boundary = zone.boundary || zone.polygon_coordinates;
        if (boundary) {
            let latlngs = [];
            // Parse GeoJSON coordinates
            if (boundary.coordinates && boundary.coordinates[0]) {
                latlngs = boundary.coordinates[0].map(pt => [pt[1], pt[0]]);
            } else if (Array.isArray(boundary) && boundary[0]) {
                latlngs = boundary.map(pt => pt.lat ? [pt.lat, pt.lng] : [pt[0], pt[1]]);
            }

            if (latlngs.length >= 3) {
                const color = zone.status === 'active' ? '#3C6E71' : '#B44A4A';
                const poly = L.polygon(latlngs, {
                    color: color,
                    fillColor: color,
                    fillOpacity: 0.25,
                    weight: 2
                }).addTo(map);

                poly.bindTooltip(`<b>${zone.name}</b><br>هزینه پیک: ${Number(zone.delivery_price).toLocaleString()} تومان`, {
                    direction: 'top',
                    sticky: true
                });
            }
        } else if (zone.center_lat && zone.center_lng && zone.radius_km) {
            // Render circle fallback
            const circle = L.circle([zone.center_lat, zone.center_lng], {
                radius: zone.radius_km * 1000,
                color: '#284B63',
                fillOpacity: 0.15,
                weight: 1
            }).addTo(map);

            circle.bindTooltip(`<b>${zone.name} (شعاع تقریبی)</b><br>هزینه پیک: ${Number(zone.delivery_price).toLocaleString()} تومان`, {
                direction: 'top',
                sticky: true
            });
        }
    });

    // Add Leaflet Draw Toolbar (Polygon only)
    const drawControl = new L.Control.Draw({
        position: 'topleft',
        draw: {
            polygon: {
                allowIntersection: false,
                drawError: {
                    color: '#B44A4A',
                    message: 'خطوط چندضلعی نمی‌توانند همدیگر را قطع کنند!'
                },
                shapeOptions: {
                    color: '#284B63',
                    fillColor: '#284B63',
                    fillOpacity: 0.4
                }
            },
            polyline: false,
            circle: false,
            rectangle: false,
            marker: false,
            circlemarker: false
        },
        edit: {
            featureGroup: drawnItems
        }
    });
    map.addControl(drawControl);

    // Handle Draw Events
    map.on(L.Draw.Event.CREATED, function (event) {
        drawnItems.clearLayers();
        const layer = event.layer;
        drawnItems.addLayer(layer);

        const geoJson = layer.toGeoJSON();
        const inputEl = document.getElementById('create-boundary-input');
        if (inputEl) {
            inputEl.value = JSON.stringify(geoJson.geometry);
            inputEl.dispatchEvent(new Event('input'));
        }

        const pointCount = geoJson.geometry.coordinates[0]?.length || 0;
        const alpineEl = document.querySelector('[x-data]');
        if (alpineEl && alpineEl._x_dataStack) {
            alpineEl._x_dataStack[0].newBoundary = JSON.stringify(geoJson.geometry);
            alpineEl._x_dataStack[0].pointCount = pointCount;
        }
    });

    document.getElementById('clear-drawn-btn')?.addEventListener('click', function () {
        drawnItems.clearLayers();
        const inputEl = document.getElementById('create-boundary-input');
        if (inputEl) {
            inputEl.value = '';
            inputEl.dispatchEvent(new Event('input'));
        }
        const alpineEl = document.querySelector('[x-data]');
        if (alpineEl && alpineEl._x_dataStack) {
            alpineEl._x_dataStack[0].newBoundary = '';
            alpineEl._x_dataStack[0].pointCount = 0;
        }
    });
});
</script>
@endsection
