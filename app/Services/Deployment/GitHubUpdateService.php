<?php

namespace App\Services\Deployment;

use App\Models\Deployment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GitHubUpdateService
{
    protected string $repo;
    protected string $branch;
    protected ?string $token;

    public function __construct()
    {
        $this->repo = config('deployment.github_repo', 'abaharloo4/karasu-cafe');
        $this->branch = config('deployment.github_branch', 'main');
        $this->token = config('deployment.github_token');
    }

    /**
     * Get current deployed commit SHA from database or local git.
     */
    public function getCurrentDeployedCommit(): array
    {
        $latestSuccessful = Deployment::where('status', 'success')
            ->latest('finished_at')
            ->first();

        if ($latestSuccessful) {
            return [
                'sha' => $latestSuccessful->commit_sha,
                'short_sha' => $latestSuccessful->short_sha,
                'message' => $latestSuccessful->commit_message,
                'author' => $latestSuccessful->commit_author,
                'deployed_at' => $latestSuccessful->finished_at,
                'source' => 'deployments_table',
            ];
        }

        // Fallback: check local git repository if available
        $localSha = trim(shell_exec('git rev-parse HEAD 2>nul || git rev-parse HEAD 2>/dev/null') ?? '');
        if ($localSha && strlen($localSha) >= 7) {
            return [
                'sha' => $localSha,
                'short_sha' => substr($localSha, 0, 7),
                'message' => 'نسخه محلی اولیه',
                'author' => 'System',
                'deployed_at' => null,
                'source' => 'local_git',
            ];
        }

        return [
            'sha' => '0000000',
            'short_sha' => '0000000',
            'message' => 'اولین نصب (نسخه اولیه)',
            'author' => 'B Cafe',
            'deployed_at' => null,
            'source' => 'initial',
        ];
    }

    /**
     * Check GitHub for new commits on the main branch.
     */
    public function checkForUpdates(): array
    {
        $current = $this->getCurrentDeployedCommit();
        $currentSha = $current['sha'];

        $headers = [
            'Accept' => 'application/vnd.github.v3+json',
            'User-Agent' => 'BCafe-AutoUpdater/1.0',
        ];

        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }

        try {
            $sslVerify = !app()->isLocal();

            // 1. Fetch latest commit on target branch
            $url = "https://api.github.com/repos/{$this->repo}/commits/{$this->branch}";
            $response = Http::withHeaders($headers)
                ->withOptions(['verify' => $sslVerify])
                ->timeout(12)
                ->get($url);

            if (!$response->successful()) {
                $status = $response->status();
                $body = $response->json();
                $errorMsg = $body['message'] ?? 'خطا در ارتباط با گیت‌هاب';

                if ($errorMsg === 'Git Repository is empty.') {
                    $errorMsg = 'مخزن گیت‌هاب در حال حاضر خالی است یا هنوز کامیت جدیدی روی شاخه اصلی (main) ارسال نشده است.';
                } elseif ($status === 403 && str_contains($errorMsg, 'rate limit')) {
                    $errorMsg = 'محدودیت تعداد درخواست GitHub به پایان رسیده است. در صورت نیاز توکن API گیت‌هاب را در تنظیمات وارد فرمایید.';
                }

                return [
                    'success' => false,
                    'has_update' => false,
                    'error' => $errorMsg,
                    'current' => $current,
                ];
            }

            $latestCommit = $response->json();
            $latestSha = $latestCommit['sha'] ?? '';
            $latestShortSha = substr($latestSha, 0, 7);
            $commitMessage = $latestCommit['commit']['message'] ?? '';
            $authorName = $latestCommit['commit']['author']['name'] ?? 'GitHub';
            $commitDate = $latestCommit['commit']['author']['date'] ?? null;

            $hasUpdate = ($currentSha !== '0000000' && $currentSha !== $latestSha && !str_starts_with($latestSha, $currentSha));

            // 2. If update available, fetch recent commits diff
            $commitsList = [];
            if ($hasUpdate) {
                $commitsUrl = "https://api.github.com/repos/{$this->repo}/commits?sha={$this->branch}&per_page=8";
                $commitsResp = Http::withHeaders($headers)
                    ->withOptions(['verify' => $sslVerify])
                    ->timeout(10)
                    ->get($commitsUrl);
                if ($commitsResp->successful()) {
                    foreach ($commitsResp->json() as $item) {
                        $sha = $item['sha'] ?? '';
                        if ($sha === $currentSha || str_starts_with($sha, $currentSha)) {
                            break;
                        }
                        $commitsList[] = [
                            'sha' => $sha,
                            'short_sha' => substr($sha, 0, 7),
                            'message' => $item['commit']['message'] ?? '',
                            'author' => $item['commit']['author']['name'] ?? '',
                            'date' => $item['commit']['author']['date'] ?? '',
                            'url' => $item['html_url'] ?? '',
                        ];
                    }
                }
            }

            return [
                'success' => true,
                'has_update' => $hasUpdate,
                'current' => $current,
                'latest' => [
                    'sha' => $latestSha,
                    'short_sha' => $latestShortSha,
                    'message' => $commitMessage,
                    'author' => $authorName,
                    'date' => $commitDate,
                    'url' => $latestCommit['html_url'] ?? '',
                ],
                'pending_commits_count' => count($commitsList),
                'commits' => $commitsList,
            ];
        } catch (\Throwable $e) {
            Log::error('GitHub update check failed: ' . $e->getMessage());
            return [
                'success' => false,
                'has_update' => false,
                'error' => 'خطا در ارتباط شبکه با مخزن گیت‌هاب: ' . $e->getMessage(),
                'current' => $current,
            ];
        }
    }
}
