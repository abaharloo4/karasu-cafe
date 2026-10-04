@extends('errors.error-layout')

@section('title', '۵۰۳ - کافه موقتاً در حال استراحت است')
@section('badge', 'بی کافه | در حال سرویس موقت')
@section('code', '۵۰۳')
@section('heading', 'کافه موقتاً در حال استراحت و آماده‌سازی است')
@section('description', 'ما در حال سرویس دوره‌ای تجهیزات و آماده‌سازی دانه‌های تازه قهوه هستیم. این وقفه کوتاه است و به زودی با یک فنجان تازه‌دم در خدمت شما خواهیم بود.')

@section('illustration')
<svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="#5EEAD4" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <!-- Coffee bag / setup with clock -->
    <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
    <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
    <path d="M6 1v3"></path>
    <path d="M10 1v3"></path>
    <path d="M14 1v3"></path>
    <circle cx="10" cy="13" r="3" stroke="#F59E0B"></circle>
    <polyline points="10 11.5 10 13 11.5 14" stroke="#F59E0B"></polyline>
</svg>
@endsection

@section('extra_action')
<button type="button" onclick="window.location.reload()" class="btn btn-secondary" style="border-color: rgba(94, 234, 212, 0.4); color: #5EEAD4;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M23 4v6h-6"></path>
        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
    </svg>
    <span>بارگذاری مجدد صفحه</span>
</button>
@endsection
