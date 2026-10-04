@extends('errors.error-layout')

@section('title', '۴۲۹ - درخواست‌های بیش از حد')
@section('badge', 'بی کافه | محدودیت تعداد درخواست')
@section('code', '۴۲۹')
@section('heading', 'باریستا چند ثانیه به استراحت نیاز دارد!')
@section('description', 'تعداد درخواست‌های ارسالی از دستگاه شما در مدت کوتاهی بیش از حد مجاز بوده است. برای حفظ پایداری سیستم، لطفاً چند ثانیه نفس تازه کنید و دوباره تلاش فرمایید.')

@section('illustration')
<svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <!-- Pressure gauge / Speedometer -->
    <path d="M12 2a10 10 0 0 0-7.07 17.07l1.41-1.41A8 8 0 1 1 19.66 17.66l1.41 1.41A10 10 0 0 0 12 2z"></path>
    <!-- Dial needle in high zone -->
    <line x1="12" y1="12" x2="16" y2="7" stroke="#EF4444" stroke-width="2.2"></line>
    <circle cx="12" cy="12" r="2" fill="#EF4444"></circle>
</svg>
@endsection

@section('extra_action')
<button type="button" onclick="setTimeout(() => window.location.reload(), 1000)" class="btn btn-secondary">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="10"></circle>
        <polyline points="12 6 12 12 16 14"></polyline>
    </svg>
    <span>صبر و تلاش مجدد</span>
</button>
@endsection
