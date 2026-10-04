<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Deployment;
use App\Services\Deployment\CPanelDeploymentService;
use App\Services\Deployment\GitHubUpdateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class DeploymentController extends Controller
{
    public function __construct(
        protected GitHubUpdateService $gitHubService,
        protected CPanelDeploymentService $cpanelService
    ) {}

    /**
     * Display the deployments & updates management dashboard.
     */
    public function index(): View
    {
        $current = $this->gitHubService->getCurrentDeployedCommit();
        $deployments = Deployment::with('triggerUser')
            ->latest()
            ->paginate(15);

        $isConfigured = $this->cpanelService->isConfigured();

        // Check for latest backups in ~/backups/db or storage/backups/db
        $homeDir = getenv('HOME') ?: (isset($_SERVER['DOCUMENT_ROOT']) ? dirname($_SERVER['DOCUMENT_ROOT']) : storage_path());
        $backupDir = $homeDir . '/backups/db';
        if (!File::isDirectory($backupDir)) {
            $backupDir = storage_path('backups/db');
        }

        $backups = [];
        if (File::isDirectory($backupDir)) {
            $files = File::glob($backupDir . '/db_*.sql.gz');
            usort($files, fn($a, $b) => filemtime($b) - filemtime($a)); // newest first
            foreach (array_slice($files, 0, 5) as $f) {
                $backups[] = [
                    'name' => basename($f),
                    'size' => round(File::size($f) / 1024, 1) . ' KB',
                    'date' => date('Y-m-d H:i:s', filemtime($f)),
                ];
            }
        }

        return view('deployments.index', compact('current', 'deployments', 'isConfigured', 'backups'));
    }

    /**
     * Check GitHub for available updates (AJAX).
     */
    public function check(): JsonResponse
    {
        $result = $this->gitHubService->checkForUpdates();
        return response()->json($result);
    }

    /**
     * Trigger deployment via cPanel (AJAX/POST).
     */
    public function trigger(Request $request): JsonResponse
    {
        $commitSha = $request->input('commit_sha');
        $commitMessage = $request->input('commit_message', 'استقرار نسخه جدید از پنل');
        $commitAuthor = $request->input('commit_author', auth()->user()?->name ?? 'مدیر');

        // Avoid parallel deployments if one is already running
        $alreadyRunning = Deployment::where('status', 'running')->exists();
        if ($alreadyRunning) {
            return response()->json([
                'success' => false,
                'error' => 'یک عملیات استقرار در حال حاضر روی سرور در حال اجرا است. لطفاً تا اتمام آن شکیبا باشید.',
            ], 422);
        }

        $result = $this->cpanelService->triggerDeployment([
            'sha' => $commitSha ?: 'HEAD',
            'message' => $commitMessage,
            'author' => $commitAuthor,
        ], auth()->id());

        return response()->json($result);
    }

    /**
     * Poll status of an ongoing deployment (AJAX).
     */
    public function status(Deployment $deployment): JsonResponse
    {
        $status = $this->cpanelService->getDeploymentStatus($deployment);
        return response()->json($status);
    }

    /**
     * Web endpoint for resetting OPcache (used by deploy.sh post-flip).
     */
    public function opcacheClear(Request $request): JsonResponse
    {
        $providedKey = $request->query('key');
        $expectedKey = config('deployment.deploy_secret', 'bcafe-deploy-secret-token');

        if (!hash_equals((string) $expectedKey, (string) $providedKey)) {
            return response()->json(['error' => 'Unauthorized key'], 403);
        }

        $cleared = false;
        if (function_exists('opcache_reset')) {
            $cleared = opcache_reset();
        }

        return response()->json([
            'success' => true,
            'opcache_cleared' => $cleared,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
