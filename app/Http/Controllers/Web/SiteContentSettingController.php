<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SiteContentSetting;
use Illuminate\Http\Request;

class SiteContentSettingController extends Controller
{
    /**
     * Display the CMS & Site Content management page.
     */
    public function edit()
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $setting = SiteContentSetting::getForCafe($cafeId);

        return view('settings.site-content', compact('setting'));
    }

    /**
     * Update the site content and aspect ratio settings.
     */
    public function update(Request $request)
    {
        $cafeId = auth()->user()->cafe_id ?? 1;
        $setting = SiteContentSetting::getForCafe($cafeId);

        $validated = $request->validate([
            'menu_image_aspect' => 'required|in:standard,square,portrait,video',
            'menu_title' => 'nullable|string|max:150',
            'menu_subtitle' => 'nullable|string|max:250',
            'about_title' => 'nullable|string|max:150',
            'about_subtitle' => 'nullable|string|max:250',
            'about_story' => 'nullable|string',
            'about_address' => 'nullable|string|max:300',
            'about_phone' => 'nullable|string|max:50',
            'about_instagram' => 'nullable|string|max:100',
            'about_map_lat' => 'nullable|string|max:30',
            'about_map_lng' => 'nullable|string|max:30',
            'about_map_neshan_url' => 'nullable|string|max:255',
            'about_map_balad_url' => 'nullable|string|max:255',
            'about_map_google_url' => 'nullable|string|max:255',
            'subscriptions_title' => 'nullable|string|max:150',
            'subscriptions_subtitle' => 'nullable|string|max:250',
            'subscriptions_description' => 'nullable|string',
            'subscriptions_rules' => 'nullable|string',
            'footer_text' => 'nullable|string|max:500',
            'footer_hours' => 'nullable|string|max:200',
        ]);

        $setting->update($validated);

        return redirect()->route('settings.site-content.edit')
            ->with('success', 'محتوا، متون سایت و ابعاد تصاویر منو با موفقیت به‌روزرسانی شد.');
    }
}
