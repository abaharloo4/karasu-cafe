<?php

namespace App\Services\Deployment;

use App\Models\Deployment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CPanelDeploymentService
{
    protected string $host;
    protected int $port;
    protected string $username;
    protected string $apiToken;
    protected string $repositoryRoot;
    protected bool $sslVerify;

    public function __construct()
    {
        $this->host = config('deployment.cpanel.host', 'localhost');
        $this->port = (int) config('deployment.cpanel.port', 2083);
        $this->username = config('deployment.cpanel.username', '');
        $this->apiToken = config('deployment.cpanel.api_token', '');
        $this->repositoryRoot = config('deployment.cpanel.repository_root', '');
        $this->sslVerify = (bool) config('deployment.cpanel.ssl_verify', false);
    }

    /**
     * Check if cPanel credentials are fully configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->username) && !empty($this->apiToken) && !empty($this->repositoryRoot);
    }

    /**
     * Trigger deployment execution via cPanel UAPI.
     */
    public function triggerDeployment(array $commitInfo, ?int $userId = null): array
    {
        $commitSha = $commitInfo['sha'] ?? 'unknown';
        $commitMessage = $commitInfo['message'] ?? 'استقرار نسخه جدید';
        $commitAuthor = $commitInfo['author'] ?? 'Admin';

        // 1. Create Deployment record in database
        $deployment = Deployment::create([
            'cafe_id' => 1,
            'commit_sha' => $commitSha,
            'commit_message' => $commitMessage,
            'commit_author' => $commitAuthor,
            'started_at' => Carbon::now(),
            'status' => 'running',
            'triggered_by' => $userId,
            'log' => "شروع استقرار به نسخه {$commitSha}...\n",
        ]);

        if (!$this->isConfigured()) {
            // Check if local deploy.sh exists and can be tested
            $deployScript = base_path('deploy.sh');
            if (file_exists($deployScript) && app()->environment('local', 'testing')) {
                $msg = "توکن cPanel در تنظیمات (.env) تنظیم نشده است. سیستم در محیط آزمایشی قرار دارد.\n" .
                       "اسکریپت deploy.sh در مخزن آماده و معتبر است.\n";
                $deployment->update([
                    'status' => 'success',
                    'finished_at' => Carbon::now(),
                    'log' => $deployment->log . $msg,
                ]);

                return [
                    'success' => true,
                    'is_simulation' => true,
                    'deployment_id' => $deployment->id,
                    'message' => 'استقرار در محیط محلی شبیه‌سازی شد (تنظیمات cPanel را برای سرور اصلی در .env وارد کنید).',
                ];
            }

            $errorMsg = 'اطلاعات احراز هویت cPanel (نام کاربری، توکن API یا مسیر مخزن) در فایل .env تعریف نشده است.';
            $deployment->update([
                'status' => 'failed',
                'finished_at' => Carbon::now(),
                'log' => $deployment->log . "خطا: {$errorMsg}\n",
            ]);

            return [
                'success' => false,
                'deployment_id' => $deployment->id,
                'error' => $errorMsg,
            ];
        }

        try {
            // 2. Call UAPI: VersionControlManager::update (Git Pull)
            $updateResp = $this->callUapi('VersionControlManager', 'update', [
                'repository_root' => $this->repositoryRoot,
            ]);

            if (!$updateResp['status']) {
                $err = implode(' | ', $updateResp['errors'] ?? ['خطا در اجرای Git Pull توسط cPanel']);
                $deployment->update([
                    'status' => 'failed',
                    'finished_at' => Carbon::now(),
                    'log' => $deployment->log . "خطا در pull مخزن:\n" . $err . "\n",
                ]);

                return [
                    'success' => false,
                    'deployment_id' => $deployment->id,
                    'error' => 'خطا در گیت‌پول cPanel: ' . $err,
                ];
            }

            $deployment->update([
                'log' => $deployment->log . "مخزن با موفقیت از گیت‌هاب دریافت (pull) شد.\nدر حال شروع عملیات استقرار (.cpanel.yml -> deploy.sh)...\n",
            ]);

            // 3. Call UAPI: VersionControlManager::create_deployment (Run .cpanel.yml)
            $deployResp = $this->callUapi('VersionControlManager', 'create_deployment', [
                'repository_root' => $this->repositoryRoot,
            ]);

            if (!$deployResp['status']) {
                $err = implode(' | ', $deployResp['errors'] ?? ['خطا در آغاز تسک استقرار cPanel']);
                $deployment->update([
                    'status' => 'failed',
                    'finished_at' => Carbon::now(),
                    'log' => $deployment->log . "خطا در ساخت دستور استقرار:\n" . $err . "\n",
                ]);

                return [
                    'success' => false,
                    'deployment_id' => $deployment->id,
                    'error' => 'خطا در ایجاد تسک استقرار cPanel: ' . $err,
                ];
            }

            $taskId = $deployResp['data']['task_id'] ?? null;
            $deployment->update([
                'cpanel_task_id' => $taskId,
                'log' => $deployment->log . "تسک استقرار سرور با شناسه [{$taskId}] در حال اجرا است.\n",
            ]);

            return [
                'success' => true,
                'deployment_id' => $deployment->id,
                'cpanel_task_id' => $taskId,
                'message' => 'دستور استقرار با موفقیت به سرور cPanel ارسال شد.',
            ];
        } catch (\Throwable $e) {
            Log::error('cPanel UAPI deployment exception: ' . $e->getMessage());
            $deployment->update([
                'status' => 'failed',
                'finished_at' => Carbon::now(),
                'log' => $deployment->log . "استثنا در ارتباط با cPanel UAPI: " . $e->getMessage() . "\n",
            ]);

            return [
                'success' => false,
                'deployment_id' => $deployment->id,
                'error' => 'خطای ارتباطی با وب‌سرویس cPanel: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Poll deployment log & status from cPanel.
     */
    public function getDeploymentStatus(Deployment $deployment): array
    {
        if ($deployment->status !== 'running') {
            return [
                'status' => $deployment->status,
                'finished_at' => $deployment->finished_at?->format('H:i:s'),
                'log' => $deployment->log,
                'backup_path' => $deployment->backup_path,
            ];
        }

        if (!$this->isConfigured()) {
            return [
                'status' => $deployment->status,
                'finished_at' => null,
                'log' => $deployment->log,
                'backup_path' => $deployment->backup_path,
            ];
        }

        try {
            $logResp = $this->callUapi('VersionControlManager', 'retrieve_deploy_log', [
                'repository_root' => $this->repositoryRoot,
            ]);

            if ($logResp['status'] && !empty($logResp['data']['log'])) {
                $serverLog = $logResp['data']['log'];
                $isCompleted = str_contains($serverLog, 'Deployment Completed Successfully') || str_contains($serverLog, 'Deployment success');
                $isFailed = str_contains($serverLog, 'Deployment failed') || str_contains($serverLog, 'CRITICAL') || str_contains($serverLog, 'Aborting deployment');

                $newStatus = 'running';
                if ($isCompleted) {
                    $newStatus = 'success';
                } elseif ($isFailed) {
                    $newStatus = 'failed';
                }

                $deployment->update([
                    'log' => $serverLog,
                    'status' => $newStatus,
                    'finished_at' => ($newStatus !== 'running') ? Carbon::now() : null,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to fetch cPanel deploy log: ' . $e->getMessage());
        }

        return [
            'status' => $deployment->status,
            'finished_at' => $deployment->finished_at?->format('H:i:s'),
            'log' => $deployment->log,
            'backup_path' => $deployment->backup_path,
        ];
    }

    /**
     * Execute a cPanel UAPI call.
     */
    protected function callUapi(string $module, string $function, array $params = []): array
    {
        $url = "https://{$this->host}:{$this->port}/execute/{$module}/{$function}";

        $headers = [
            'Authorization' => "cpanel {$this->username}:{$this->apiToken}",
            'Accept' => 'application/json',
        ];

        $response = Http::withHeaders($headers)
            ->withOptions(['verify' => $this->sslVerify])
            ->timeout(60)
            ->post($url, $params);

        if (!$response->successful()) {
            return [
                'status' => 0,
                'errors' => ['HTTP Error ' . $response->status() . ': ' . $response->body()],
                'data' => null,
            ];
        }

        return $response->json();
    }
}
