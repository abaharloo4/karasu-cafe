@extends('errors.error-layout')

@section('title', '۴۱۹ - نشست منقضی شد')
@section('badge', 'بی کافه | انقضای اعتبار نشست')
@section('code', '۴۱۹')
@section('heading', 'قهوه کمی سرد شد! نشست شما منقضی شده است')
@section('description', 'به دلیل طولانی شدن زمان حضور در این صفحه یا ارسال مجدد فرم، توکن امنیتی منقضی گردید. برای ادامه کار، کافیست صفحه را مجدداً بارگذاری کنید.')

@section('illustration')
<svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <!-- Hourglass shape -->
    <path d="M5 22h14"></path>
    <path d="M5 2h14"></path>
    <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"></path>
    <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"></path>

    <!-- Coffee bean drop in center -->
    <circle cx="12" cy="17" r="1.5" fill="#F59E0B"></circle>
</svg>
@endsection

@section('extra_action')
<button type="button" onclick="window.location.reload()" class="btn btn-secondary" style="border-color: rgba(245, 158, 11, 0.4); color: #FCD34D;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M23 4v6h-6"></path>
        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
    </svg>
    <span>بارگذاری مجدد صفحه</span>
</button>
@endsection
