<?php

namespace App\Console\Commands;

use App\Models\Deployment;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;

class DeploymentRecordSuccessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deployment:record-success 
                            {--commit= : Target Git Commit SHA}
                            {--message= : Commit message}
                            {--author= : Commit author}
                            {--log= : Path to deployment log file}
                            {--backup= : Path to DB backup file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record a successful deployment execution in the database audit log';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $commit = $this->option('commit') ?: 'HEAD';
        $message = $this->option('message') ?: 'cPanel Git Deployment';
        $author = $this->option('author') ?: 'cPanel Auto';
        $logPath = $this->option('log');
        $backupPath = $this->option('backup');

        $logContent = null;
        if ($logPath && File::exists($logPath)) {
            $logContent = File::get($logPath);
        }

        // Find active/pending deployment or create new record
        $deployment = Deployment::where('status', 'running')
            ->orWhere('status', 'pending')
            ->latest()
            ->first();

        if (!$deployment) {
            $deployment = new Deployment();
            $deployment->started_at = Carbon::now()->subSeconds(30);
        }

        $deployment->commit_sha = $commit;
        $deployment->commit_message = $message;
        $deployment->commit_author = $author;
        $deployment->backup_path = $backupPath;
        $deployment->status = 'success';
        $deployment->finished_at = Carbon::now();
        if ($logContent) {
            $deployment->log = $logContent;
        }
        $deployment->save();

        $this->info("Deployment recorded successfully: ID #{$deployment->id} ({$deployment->short_sha})");

        return Command::SUCCESS;
    }
}
