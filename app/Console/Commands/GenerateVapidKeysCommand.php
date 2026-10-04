<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webpush:vapid {--force : Overwrite existing keys in .env}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate VAPID public and private keys for Web Push notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!getenv('OPENSSL_CONF')) {
            $candidates = [
                'C:/wamp64/bin/php/php8.3.14/extras/ssl/openssl.cnf',
                'C:/wamp64/bin/apache/apache2.4.62.1/conf/openssl.cnf',
            ];
            foreach ($candidates as $cand) {
                if (file_exists($cand)) {
                    putenv("OPENSSL_CONF={$cand}");
                    break;
                }
            }
        }

        $keys = VAPID::createVapidKeys();

        $publicKey = $keys['publicKey'];
        $privateKey = $keys['privateKey'];

        $this->info('VAPID Keys generated successfully:');
        $this->line("<comment>Public Key:</comment>  {$publicKey}");
        $this->line("<comment>Private Key:</comment> {$privateKey}");

        $envPath = base_path('.env');
        if (File::exists($envPath)) {
            $envContent = File::get($envPath);

            $hasPublic = str_contains($envContent, 'VAPID_PUBLIC_KEY=');
            $hasPrivate = str_contains($envContent, 'VAPID_PRIVATE_KEY=');

            if ((!$hasPublic && !$hasPrivate) || $this->option('force')) {
                // Remove existing if force
                if ($this->option('force')) {
                    $envContent = preg_replace('/VAPID_PUBLIC_KEY=.*\n?/', '', $envContent);
                    $envContent = preg_replace('/VAPID_PRIVATE_KEY=.*\n?/', '', $envContent);
                    $envContent = preg_replace('/VAPID_SUBJECT=.*\n?/', '', $envContent);
                }

                $envContent .= "\n# Web Push VAPID Keys\n";
                $envContent .= "VAPID_PUBLIC_KEY={$publicKey}\n";
                $envContent .= "VAPID_PRIVATE_KEY={$privateKey}\n";
                $envContent .= "VAPID_SUBJECT=mailto:admin@bcafe.ir\n";

                File::put($envPath, $envContent);
                $this->info('VAPID keys written to .env file successfully.');
            } else {
                $this->warn('VAPID keys already exist in .env. Use --force to overwrite.');
            }
        }

        return Command::SUCCESS;
    }
}
