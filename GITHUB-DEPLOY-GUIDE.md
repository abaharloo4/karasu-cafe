# راهنمای به‌روزرسانی پروژه با گیت‌هاب (GitHub Deployment Guide)
## نسخه فعلی: v2.0.1

این پروژه به یک سیستم کامل استقرار و به‌روزرسانی بدون قطعی (Zero-Downtime Git Deployment) مجهز است که ارتباط مستقیمی بین مخزن گیت‌هاب (`abaharloo4/karasu-cafe`)، پنل مدیریت ادمین و هاست سی‌پنل برقرار می‌کند.

---

### ۱. ساختار کامیت و تگ نسخه 2.0.1

برای ارسال این نسخه به گیت‌هاب و ایجاد ریلیز رسمی، دستورات زیر را در ریشه پروژه اجرا کنید:

```bash
# ۱. اضافه کردن ریموت مخزن گیت‌هاب (در صورت نیاز)
git remote add origin https://github.com/abaharloo4/karasu-cafe.git

# ۲. اطمینان از قرار داشتن روی شاخه main
git branch -M main

# ۳. استیج کردن فایل‌ها و ثبت کامیت نسخه 2.0.1
git add .
git commit -m "chore(release): bump version to 2.0.1 - fix cache serialization & harden cPanel deployment"

# ۴. ثبت تگ نگارش
git tag -a v2.0.1 -m "Release v2.0.1: cPanel production ready & cache fix"

# ۵. ارسال تغییرات و تگ‌ها به گیت‌هاب
git push -u origin main --tags
```

---

### ۲. عملکرد سیستم آپدیت خودکار در پنل ادمین (`/admin/deployments`)

1. **بررسی آپدیت از گیت‌هاب (Check for Updates):**
   - با کلیک روی دکمه «بررسی آپدیت» در پنل ادمین، سرویس `GitHubUpdateService` آخرین کامیت‌ها و ریلیزهای مخزن `abaharloo4/karasu-cafe` را از GitHub API استعلام می‌کند.
   - تفاوت کامیت‌های جدید (Diff) و لیست تغییرات به همراه نام توسعه‌دهنده به صورت زنده نمایش داده می‌شود.

2. **اعمال آپدیت خودکار (Trigger Deploy):**
   - با زدن دکمه «اجرای آپدیت»، سرویس `CPanelDeploymentService` اسکریپت `deploy.sh` را اجرا می‌کند.
   - فرآیند به صورت اتمیک انجام می‌شود:
     * پشتیبان‌گیری خودکار از دیتابیس MySQL (`mysqldump`)
     * ساخت ریلیز جدید در پوشه `releases/`
     * اجرای مایگریشن‌های امن (`migrate --force`)
     * ساخت مجدد کش‌های لاراول (`config:cache`, `route:cache`, `view:cache`)
     * جابجایی بدون قطعی لینک سیمبولیک (`ln -sfn`)
     * پاکسازی OPcache سرور

---

### ۳. استقرار خودکار با cPanel Git Version Control

در سی‌پنل از طریق منوی **Git™ Version Control**:
1. مخزن `https://github.com/abaharloo4/karasu-cafe.git` را Clone کنید.
2. شاخه را روی `main` تنظیم نمایید.
3. فایل `.cpanel.yml` در ریشه پروژه به صورت خودکار وظیفه اجرای `deploy.sh` را پس از هر `Pull` بر عهده خواهد داشت.

---

### ۴. خودکارسازی با GitHub Actions (CI/CD)

فایل `.github/workflows/deploy.yml` تعبیه شده است:
* اجرای تست‌های خودکار لاراول با PHP 8.3
* ساخت خودکار GitHub Release همراه با یادداشت‌های تغییرات هنگام Push کردن تگ‌های `v*`
* فراخوانی وب‌هوک اختیاری سی‌پنل (`CPANEL_DEPLOY_WEBHOOK_URL`)
