@php
    $panel = $pwaPanel ?? 'customer';
    $pwaSetting = \App\Models\PwaSetting::where('panel', $panel)->first() ?? \App\Models\PwaSetting::forPanel($panel);
    $themeColor = $pwaSetting->theme_color ?? ($panel === 'admin' ? '#141D22' : '#387780');
    $appName = $pwaSetting->app_name ?? 'بی کافه';
    $icon192 = $pwaSetting->icon_192 ?: asset('icons/icon-192x192.png');
@endphp

<!-- Unified PWA Web App Manifest -->
<link rel="manifest" href="{{ route('pwa.manifest.panel', ['panel' => $panel]) }}?v=2.0.2">

<!-- PWA Theme & Mobile Configuration -->
<meta name="theme-color" content="{{ $themeColor }}">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<meta name="apple-mobile-web-app-title" content="{{ $appName }}">
<link rel="apple-touch-icon" href="{{ $icon192 }}?v=2.0.2">

@if(!empty($pwaSetting->ga4_measurement_id))
<!-- Google Analytics 4 (GA4) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $pwaSetting->ga4_measurement_id }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $pwaSetting->ga4_measurement_id }}');
</script>
@endif

