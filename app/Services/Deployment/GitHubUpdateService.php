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

        // Fallback: check local git repository using pure PHP file reading (shared host & cPanel safe)
        $localSha = $this->resolveLocalGitSha();
        if ($localSha && strlen($localSha) >= 7) {
            return [
                'sha' => $localSha,
                'short_sha' => substr($localSha, 0, 7),
                'message' => 'نسخه محلی (v' . config('app.version', '2.0.2') . ')',
                'author' => 'System',
                'deployed_at' => null,
                'source' => 'local_git',
            ];
        }

        $appVersion = config('app.version', '2.0.2');

        return [
            'sha' => '0000000',
            'short_sha' => '0000000',
            'message' => "نصب اولیه (v{$appVersion})",
            'author' => 'B Cafe',
            'deployed_at' => null,
            'source' => 'initial',
        ];
    }

    /**
     * Resolve the current Git commit SHA without relying on shell_exec/exec.
     * Completely safe on shared hosting where shell functions are disabled.
     */
    protected function resolveLocalGitSha(): ?string
    {
        $gitDir = base_path('.git');
        if (!is_dir($gitDir)) {
            $parentGit = dirname(base_path()) . DIRECTORY_SEPARATOR . '.git';
            if (is_dir($parentGit)) {
                $gitDir = $parentGit;
            } else {
                return null;
            }
        }

        $headFile = $gitDir . DIRECTORY_SEPARATOR . 'HEAD';
        if (!file_exists($headFile) || !is_readable($headFile)) {
            return null;
        }

        $headContent = trim((string) @file_get_contents($headFile));
        if (empty($headContent)) {
            return null;
        }

        // Case 1: Detached HEAD (HEAD directly contains 40-char SHA)
        if (preg_match('/^[0-9a-f]{40}$/i', $headContent)) {
            return $headContent;
        }

        // Case 2: Ref pointer, e.g., "ref: refs/heads/main"
        if (str_starts_with($headContent, 'ref:')) {
            $refPath = trim(substr($headContent, 4));
            $refFile = $gitDir . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $refPath);

            if (file_exists($refFile) && is_readable($refFile)) {
                $sha = trim((string) @file_get_contents($refFile));
                if (preg_match('/^[0-9a-f]{40}$/i', $sha)) {
                    return $sha;
                }
            }

            // Case 3: Packed refs (.git/packed-refs)
            $packedRefsFile = $gitDir . DIRECTORY_SEPARATOR . 'packed-refs';
            if (file_exists($packedRefsFile) && is_readable($packedRefsFile)) {
                $packed = @file_get_contents($packedRefsFile);
                if ($packed && preg_match('/^([0-9a-f]{40})\s+' . preg_quote($refPath, '/') . '/m', $packed, $matches)) {
                    return $matches[1];
                }
            }
        }

        // Safe fallback only if shell_exec is enabled and allowed
        if (function_exists('shell_exec')) {
            try {
                $cmdOutput = trim((string) @\shell_exec('git rev-parse HEAD 2>nul || git rev-parse HEAD 2>/dev/null'));
                if (preg_match('/^[0-9a-f]{40}$/i', $cmdOutput)) {
                    return $cmdOutput;
                }
            } catch (\Throwable) {
                // Ignore any shell error
            }
        }

        return null;
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
