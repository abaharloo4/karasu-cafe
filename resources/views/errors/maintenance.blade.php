<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="6">
    <title>بی کافه (B Cafe) | در حال به‌روزرسانی کوتاه سیستم</title>
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;600;700;900&display=swap');

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
            overflow: hidden;
        }

        /* Subtle background glow */
        .glow {
            position: absolute;
            width: 380px;
            height: 380px;
            background: radial-gradient(circle, rgba(56, 119, 128, 0.28) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
            pointer-events: none;
        }

        .card {
            position: relative;
            z-index: 2;
            background: rgba(30, 41, 59, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 24px;
            max-width: 480px;
            width: 100%;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .icon-box {
            position: relative;
            width: 84px;
            height: 84px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, rgba(56, 119, 128, 0.2) 0%, rgba(56, 119, 128, 0.05) 100%);
            border: 1px solid rgba(56, 119, 128, 0.4);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ping {
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 24px;
            border: 2px solid #387780;
            animation: ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
            opacity: 0.5;
        }

        @keyframes ping {
            75%, 100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        .brand {
            font-size: 0.875rem;
            font-weight: 700;
            color: #387780;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
            display: inline-block;
            background: rgba(56, 119, 128, 0.12);
            padding: 0.25rem 0.85rem;
            border-radius: 9999px;
            border: 1px solid rgba(56, 119, 128, 0.25);
        }

        h1 {
            font-size: 1.45rem;
            font-weight: 900;
            color: #FFFFFF;
            margin-bottom: 0.85rem;
            line-height: 1.4;
        }

        p {
            font-size: 0.925rem;
            color: #94A3B8;
            line-height: 1.7;
            margin-bottom: 1.75rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.55rem 1rem;
            border-radius: 14px;
            font-size: 0.825rem;
            color: #CBD5E1;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #387780;
            box-shadow: 0 0 12px #387780;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.4; transform: scale(0.85); }
        }

        .footer-note {
            margin-top: 1.75rem;
            font-size: 0.775rem;
            color: #64748B;
        }
    </style>
</head>
<body>
    <div class="glow"></div>

    <div class="card">
        <div class="icon-box">
            <div class="ping"></div>
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#387780" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                <line x1="6" y1="1" x2="6" y2="4"></line>
                <line x1="10" y1="1" x2="10" y2="4"></line>
                <line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
        </div>

        <div class="brand">بی کافه (B Cafe)</div>
        <h1>در حال آماده‌سازی نسخه جدید</h1>
        <p>
            سیستم در حال ارتقا و دریافت به‌روزرسانی‌های جدید است. این فرآیند کاملاً خودکار بوده و معمولاً کمتر از ۳۰ ثانیه طول می‌کشد.
        </p>

        <div class="status-badge">
            <span class="pulse-dot"></span>
            <span>بارگذاری مجدد خودکار صفحه... لطفا شکیبا باشید</span>
        </div>

        <div class="footer-note">
            شیراز • سامانه آنلاین و مدیریت قهوه تخصصی بی کافه
        </div>
    </div>
</body>
</html>
