@extends('errors.error-layout')

@section('title', '۵۰۰ - خطای سرور')
@section('badge', 'بی کافه | خطای سرور ۵۰۰')
@section('code', '۵۰۰')
@section('heading', 'دستگاه اسپرسوساز سرور کمی داغ کرده!')
@section('description', 'هنگام آماده‌سازی درخواست شما، اختلالی در سمت سرور رخ داد. گزارش این خطا به صورت خودکار ثبت شده و باریستاهای فنی کافه در حال رسیدگی به آن هستند. لطفاً لحظاتی دیگر مجدداً تلاش کنید.')

@section('illustration')
<svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <!-- Server / Machine Box -->
    <rect x="2" y="2" width="20" height="8" rx="2" ry="2" stroke="#EF4444"></rect>
    <rect x="2" y="14" width="20" height="8" rx="2" ry="2" stroke="#EF4444"></rect>
    <line x1="6" y1="6" x2="6.01" y2="6" stroke="#EF4444" stroke-width="2.5"></line>
    <line x1="10" y1="6" x2="10.01" y2="6" stroke="#EF4444" stroke-width="2.5"></line>
    <line x1="6" y1="18" x2="6.01" y2="18" stroke="#EF4444" stroke-width="2.5"></line>

    <!-- Exclamation / Hazard indicator -->
    <circle cx="17" cy="18" r="2.5" stroke="#F59E0B"></circle>
    <line x1="17" y1="17" x2="17" y2="19" stroke="#F59E0B" stroke-width="1.5"></line>
</svg>
@endsection

@section('extra_action')
<button type="button" onclick="window.location.reload()" class="btn btn-secondary" style="border-color: rgba(239, 68, 68, 0.4); color: #FCA5A5;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M23 4v6h-6"></path>
        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
    </svg>
    <span>تلاش مجدد (Reload)</span>
</button>
@endsection
