@extends('errors.error-layout')

@section('title', '۴۰۴ - این فنجان پیدا نشد')
@section('badge', 'بی کافه | خطای ۴۰۴')
@section('code', '۴۰۴')
@section('heading', 'این فنجان قهوه در منو پیدا نشد!')
@section('description', 'آدرسی که وارد کردید ممکن است جابه‌جا شده باشد یا این صفحه دیگر در منوی بی کافه موجود نباشد. نگران نباشید، قهوه‌های تازه‌دم و منوی اصلی در دسترس شماست.')

@section('illustration')
<svg width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="#387780" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <!-- Steam drifting away -->
    <path d="M6 3c.8 1.2.8 2.2 0 3.2" stroke="#5EEAD4" stroke-width="1.5" stroke-dasharray="2 2" opacity="0.7">
        <animate attributeName="opacity" values="0.3;0.9;0.3" dur="2.4s" repeatCount="indefinite"/>
    </path>
    <path d="M10 2c.9 1.4.9 2.5 0 3.8" stroke="#5EEAD4" stroke-width="1.5" stroke-dasharray="2 2" opacity="0.8">
        <animate attributeName="opacity" values="0.4;1;0.4" dur="2s" repeatCount="indefinite"/>
    </path>
    <path d="M14 3c.8 1.2.8 2.2 0 3.2" stroke="#5EEAD4" stroke-width="1.5" stroke-dasharray="2 2" opacity="0.6">
        <animate attributeName="opacity" values="0.2;0.8;0.2" dur="2.6s" repeatCount="indefinite"/>
    </path>

    <!-- Coffee cup with interrogation/not found cue -->
    <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
    <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
    <line x1="2" y1="21" x2="22" y2="21" stroke="#64748B"></line>

    <!-- Magnifying glass inside cup or question mark -->
    <circle cx="10" cy="13" r="2.5" stroke="#F59E0B"></circle>
    <line x1="12" y1="15" x2="14" y2="17" stroke="#F59E0B"></line>
</svg>
@endsection
