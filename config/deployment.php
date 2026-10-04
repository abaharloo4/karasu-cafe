<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Git Repository Configuration
    |--------------------------------------------------------------------------
    | The GitHub repository used for fetching version diffs, commit logs, and releases.
    */
    'repo_url' => env('DEPLOY_REPO_URL', 'https://github.com/abaharloo4/karasu-cafe.git'),
    'github_repo' => env('DEPLOY_GITHUB_REPO', 'abaharloo4/karasu-cafe'),
    'github_branch' => env('DEPLOY_GITHUB_BRANCH', 'main'),
    'github_token' => env('GITHUB_API_TOKEN', null), // Optional, increases GitHub rate limit from 60 to 5000 req/hr

    /*
    |--------------------------------------------------------------------------
    | cPanel UAPI Credentials
    |--------------------------------------------------------------------------
    | Used to trigger VersionControlManager::update and create_deployment.
    | Create token in: cPanel -> Security -> Manage API Tokens
    */
    'cpanel' => [
        'host' => env('CPANEL_HOST', 'localhost'),
        'port' => env('CPANEL_PORT', 2083),
        'username' => env('CPANEL_USERNAME', ''),
        'api_token' => env('CPANEL_API_TOKEN', ''),
        'repository_root' => env('CPANEL_REPOSITORY_ROOT', ''), // e.g. /home/user/repositories/karasu-cafe
        'ssl_verify' => env('CPANEL_SSL_VERIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Deployment Security & Paths
    |--------------------------------------------------------------------------
    | Secret key for internal web hooks (e.g. OPcache clear).
    */
    'deploy_secret' => env('DEPLOY_SECRET', 'bcafe-deploy-secret-token'),
    'backup_retention' => (int) env('DB_BACKUP_RETENTION', 10),
    'releases_retention' => (int) env('RELEASE_RETENTION', 3),
];
