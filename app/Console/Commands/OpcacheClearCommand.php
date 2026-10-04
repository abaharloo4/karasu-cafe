<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class OpcacheClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'opcache:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear PHP OPcache bytecode cache';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!extension_loaded('Zend OPcache')) {
            $this->warn('Zend OPcache extension is not loaded.');
            return Command::SUCCESS;
        }

        if (function_exists('opcache_reset')) {
            if (opcache_reset()) {
                $this->info('PHP OPcache was successfully reset.');
                return Command::SUCCESS;
            } else {
                $this->error('Failed to reset OPcache.');
                return Command::FAILURE;
            }
        }

        $this->warn('opcache_reset() function is disabled or not available.');
        return Command::SUCCESS;
    }
}
