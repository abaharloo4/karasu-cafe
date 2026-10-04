<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'خطایی رخ داده است') | بی کافه (B Cafe)</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Vazirmatn', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #0F172A;
            color: #F8FAFC;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle ambient glow circles */
        .ambient-glow-1 {
            position: absolute;
            width: 460px;
            height: 460px;
            background: radial-gradient(circle, rgba(56, 119, 128, 0.22) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            top: 20%;
            right: 15%;
            z-index: 1;
            pointer-events: none;
        }

        .ambient-glow-2 {
            position: absolute;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(217, 119, 6, 0.15) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            bottom: 15%;
            left: 15%;
            z-index: 1;
            pointer-events: none;
        }

        .container-card {
            position: relative;
            z-index: 2;
            background: rgba(30, 41, 59, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 28px;
            max-width: 540px;
            width: 100%;
            padding: 3rem 2.25rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px) scale(0.98); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        .badge-header {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.95rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: rgba(56, 119, 128, 0.15);
            border: 1px solid rgba(56, 119, 128, 0.35);
            color: #5EEAD4;
        }

        .badge-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            box-shadow: 0 0 10px currentColor;
        }

        .illustration-wrap {
            margin: 0 auto 1.75rem;
            width: 140px;
            height: 140px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 32px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.04) 0%, rgba(255, 255, 255, 0.01) 100%);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.05);
        }

        .code-display {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.04em;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #FFFFFF 30%, #94A3B8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .heading-text {
            font-size: 1.45rem;
            font-weight: 800;
            color: #FFFFFF;
            margin-bottom: 0.85rem;
            line-height: 1.4;
        }

        .desc-text {
            font-size: 0.95rem;
            color: #94A3B8;
            line-height: 1.8;
            margin-bottom: 2.25rem;
            padding: 0 0.5rem;
        }

        .actions-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 0.85rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1.4rem;
            border-radius: 14px;
            font-size: 0.875rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.18s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            border: none;
            outline: none;
        }

        .btn:active {
            transform: scale(0.97);
        }

        .btn-primary {
            background-color: #387780;
            color: #FFFFFF;
            box-shadow: 0 4px 14px rgba(56, 119, 128, 0.35);
        }

        .btn-primary:hover {
            background-color: #2E636B;
            box-shadow: 0 6px 20px rgba(56, 119, 128, 0.45);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: #E2E8F0;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .card-footer {
            margin-top: 2.25rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.775rem;
            color: #64748B;
        }

        .brand-link {
            color: #387780;
            text-decoration: none;
            font-weight: 700;
        }

        .brand-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .container-card {
                padding: 2.25rem 1.5rem;
            }
            .code-display {
                font-size: 2.85rem;
            }
            .heading-text {
                font-size: 1.25rem;
            }
            .actions-group {
                flex-direction: column;
                width: 100%;
            }
            .btn {
                width: 100%;
            }
            .card-footer {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="ambient-glow-1"></div>
    <div class="ambient-glow-2"></div>

    <main class="container-card">
        <!-- Top Status Pill -->
        <div class="badge-header">
            <span class="badge-dot"></span>
            <span>@yield('badge', 'بی کافه | خطای سیستمی')</span>
        </div>

        <!-- Thematic Coffee Illustration -->
        <div class="illustration-wrap">
            @yield('illustration')
        </div>

        <!-- Big Code Display -->
        <div class="code-display">@yield('code', 'خطا')</div>

        <!-- Friendly Heading -->
        <h1 class="heading-text">@yield('heading', 'اتفاق غیرمنتظره‌ای رخ داده است')</h1>

        <!-- Natural Persian Description -->
        <p class="desc-text">
            @yield('description', 'صفحه یا منبع مورد نظر شما در دسترس نیست. می‌توانید به صفحه اصلی بازگردید یا مجدداً تلاش کنید.')
        </p>

        <!-- Call to Action Buttons -->
        <div class="actions-group">
            <a href="{{ url('/') }}" class="btn btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                <span>صفحه اصلی و منوی کافه</span>
            </a>

            <button type="button" onclick="window.history.length > 1 ? window.history.back() : window.location.href='/'" class="btn btn-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                <span>بازگشت به صفحه قبل</span>
            </button>
            @yield('extra_action')
        </div>

        <!-- Card Footer -->
        <div class="card-footer">
            <span>سامانه کافه و قهوه تخصصی <a href="{{ url('/') }}" class="brand-link">بی کافه (B Cafe)</a></span>
            <span>شیراز • پشتیبانی همیشه همراه شماست</span>
        </div>
    </main>
</body>
</html>
