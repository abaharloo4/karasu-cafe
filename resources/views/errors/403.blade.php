@extends('errors.error-layout')

@section('title', '۴۰۳ - دسترسی محدود شده')
@section('badge', 'بی کافه | دسترسی غیرمجاز')
@section('code', '۴۰۳')
@section('heading', 'ورود به این بخش کافه مجاز نیست!')
@section('description', 'این بخش مخصوص باریستاها یا مدیران بی کافه است و حساب شما مجوز دسترسی به آن را ندارد. اگر فکر می‌کنید اشتباهی رخ داده، وارد حساب مدیریتی خود شوید.')

@section('illustration')
<svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="#F59E0B" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <!-- Shield / Lock outline -->
    <rect x="3" y="11" width="18" height="11" rx="3" ry="3" stroke="#F59E0B"></rect>
    <path d="M7 11V7a5 5 0 0 1 10 0v4" stroke="#F59E0B"></path>
    <!-- Keyhole with coffee bean shape -->
    <circle cx="12" cy="16" r="1.5" fill="#F59E0B"></circle>
    <path d="M12 17.5v2" stroke="#F59E0B" stroke-width="2"></path>
</svg>
@endsection

@section('extra_action')
@guest
<a href="{{ route('login') }}" class="btn btn-secondary" style="border-color: rgba(245, 158, 11, 0.4); color: #FCD34D;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
        <polyline points="10 17 15 12 10 7"></polyline>
        <line x1="15" y1="12" x2="3" y2="12"></line>
    </svg>
    <span>ورود پرسنل / مدیر</span>
</a>
@endguest
@endsection
