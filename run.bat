@echo off
chcp 65001 > nul
title B Cafe - Server Launcher
echo ====================================================
echo   راه‌اندازی سرور بی کافه (B Cafe)
echo ====================================================
echo.
echo در حال راه‌اندازی سرور روی http://127.0.0.1:8000 ...
echo مرورگر به صورت خودکار باز خواهد شد.
echo.

:: Open browser automatically in 2 seconds
timeout /t 2 /nobreak > nul
start "" "http://127.0.0.1:8001"

:: Execute Artisan Serve via system WAMP PHP
"C:\wamp64\bin\php\php8.3.14\php.exe" "%~dp0artisan" serve --host=127.0.0.1 --port=8001

pause
