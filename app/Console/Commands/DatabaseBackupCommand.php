<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DatabaseBackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup {--retention=10 : Number of backups to retain}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a safe, compressed database backup and rotate old dumps';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting database backup process...');

        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');

        // Target directory: preference is ~/backups/db (outside webroot), fallback to storage/backups/db
        $homeDir = getenv('HOME') ?: (isset($_SERVER['DOCUMENT_ROOT']) ? dirname($_SERVER['DOCUMENT_ROOT']) : storage_path());
        $backupDir = $homeDir . '/backups/db';

        // Check if writable or fallback to storage
        if (!File::isDirectory($backupDir)) {
            try {
                File::makeDirectory($backupDir, 0755, true);
            } catch (\Throwable $e) {
                $backupDir = storage_path('backups/db');
                File::ensureDirectoryExists($backupDir);
            }
        }

        $timestamp = date('Y-m-d_His');
        $filename = "db_{$database}_{$timestamp}.sql.gz";
        $targetPath = $backupDir . '/' . $filename;

        // Find mysqldump binary
        $dumpBinary = $this->findMysqldump();

        if (!$dumpBinary) {
            $this->warn('mysqldump binary not found in PATH or standard paths.');
            // Fallback: Laravel schema dump if available or warn
            return Command::FAILURE;
        }

        $this->info("Creating backup at: {$targetPath}");

        $cmd = sprintf(
            '%s --host=%s --port=%s --user=%s %s --single-transaction --quick --routines --triggers %s | gzip > %s',
            escapeshellcmd($dumpBinary),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database),
            escapeshellarg($targetPath)
        );

        $returnCode = 0;
        $output = [];
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0 || !File::exists($targetPath) || File::size($targetPath) === 0) {
            $this->error('Database backup failed or generated an empty file!');
            if (File::exists($targetPath)) {
                File::delete($targetPath);
            }
            return Command::FAILURE;
        }

        $size = round(File::size($targetPath) / 1024, 2);
        $this->info("Database backup created successfully! ({$size} KB)");

        // Rotation: Keep last N backups
        $retention = (int) ($this->option('retention') ?: config('deployment.backup_retention', 10));
        $this->rotateBackups($backupDir, $retention);

        return Command::SUCCESS;
    }

    private function findMysqldump(): ?string
    {
        $candidates = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/cpanel/ea-mysql80/root/usr/bin/mysqldump',
            '/opt/cpanel/ea-mariadb106/root/usr/bin/mysqldump',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.3.0\\bin\\mysqldump.exe',
        ];

        // Check PATH
        $pathBinary = trim(shell_exec(DIRECTORY_SEPARATOR === '\\' ? 'where mysqldump 2>nul' : 'which mysqldump 2>/dev/null') ?? '');
        if (!empty($pathBinary) && !str_contains($pathBinary, 'Could not find')) {
            $first = explode("\n", $pathBinary)[0];
            return trim($first);
        }

        foreach ($candidates as $cand) {
            if (file_exists($cand) && is_executable($cand)) {
                return $cand;
            }
        }

        return null;
    }

    private function rotateBackups(string $dir, int $keep): void
    {
        $files = File::glob($dir . '/db_*.sql.gz');
        if (count($files) <= $keep) {
            return;
        }

        // Sort oldest first
        usort($files, fn($a, $b) => filemtime($a) - filemtime($b));

        $toDelete = count($files) - $keep;
        for ($i = 0; $i < $toDelete; $i++) {
            $this->line("Rotating out old backup: " . basename($files[$i]));
            File::delete($files[$i]);
        }
    }
}
