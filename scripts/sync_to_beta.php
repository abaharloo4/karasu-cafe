<?php

$root = realpath(__DIR__ . '/..');
$beta = $root . '/beta';

if (!is_dir($beta)) {
    die("Beta directory not found: {$beta}\n");
}

echo "=== Syncing Karasu Cafe to Beta Package ===\n";

// Helper to recursively copy directories with exclusion
function syncDir($src, $dst, $exclusions = []) {
    if (!is_dir($dst)) {
        mkdir($dst, 0755, true);
    }

    $dir = opendir($src);
    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $srcPath = $src . '/' . $file;
        $dstPath = $dst . '/' . $file;

        // Check exclusions
        $skip = false;
        foreach ($exclusions as $ex) {
            if ($file === $ex || fnmatch($ex, $file)) {
                $skip = true;
                break;
            }
        }
        if ($skip) {
            continue;
        }

        if (is_dir($srcPath)) {
            syncDir($srcPath, $dstPath, $exclusions);
        } else {
            copy($srcPath, $dstPath);
        }
    }
    closedir($dir);
}

// Clean target directory before sync
function cleanDirExcept($dir, $keep = []) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..' || in_array($item, $keep)) {
            continue;
        }
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            deleteDir($path);
        } else {
            unlink($path);
        }
    }
}

function deleteDir($dir) {
    if (!is_dir($dir)) return;
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        is_dir($path) ? deleteDir($path) : unlink($path);
    }
    rmdir($dir);
}

// 1. Sync app/
echo "Syncing app/...\n";
cleanDirExcept($beta . '/app');
syncDir($root . '/app', $beta . '/app');

// 2. Sync bootstrap/ (exclude cache/*.php)
echo "Syncing bootstrap/...\n";
syncDir($root . '/bootstrap', $beta . '/bootstrap', ['cache']);
if (!is_dir($beta . '/bootstrap/cache')) {
    mkdir($beta . '/bootstrap/cache', 0755, true);
}
file_put_contents($beta . '/bootstrap/cache/.gitignore', "*\n!.gitignore\n");

// 3. Sync config/
echo "Syncing config/...\n";
cleanDirExcept($beta . '/config');
syncDir($root . '/config', $beta . '/config');

// 4. Sync database/
echo "Syncing database/...\n";
cleanDirExcept($beta . '/database');
syncDir($root . '/database', $beta . '/database');

// 5. Sync resources/
echo "Syncing resources/...\n";
cleanDirExcept($beta . '/resources');
syncDir($root . '/resources', $beta . '/resources');

// 6. Sync routes/
echo "Syncing routes/...\n";
cleanDirExcept($beta . '/routes');
syncDir($root . '/routes', $beta . '/routes');

// 7. Sync public/ (excluding hot, screenshots, storage symlink)
echo "Syncing public/...\n";
syncDir($root . '/public', $beta . '/public', ['hot', 'storage', 'screenshots']);

// 8. Ensure storage/ clean structure
echo "Verifying storage/...\n";
$storageDirs = [
    'app',
    'app/public',
    'app/private',
    'app/private/card-receipts',
    'framework',
    'framework/cache',
    'framework/cache/data',
    'framework/sessions',
    'framework/views',
    'logs'
];
foreach ($storageDirs as $sDir) {
    $fullPath = $beta . '/storage/' . $sDir;
    if (!is_dir($fullPath)) {
        mkdir($fullPath, 0755, true);
    }
    if (!file_exists($fullPath . '/.gitignore')) {
        file_put_contents($fullPath . '/.gitignore', "*\n!.gitignore\n");
    }
}

// 9. Root files
echo "Syncing root files...\n";
copy($root . '/artisan', $beta . '/artisan');
copy($root . '/composer.json', $beta . '/composer.json');
if (file_exists($root . '/composer.lock')) {
    copy($root . '/composer.lock', $beta . '/composer.lock');
}

echo "=== Sync to Beta complete! ===\n";
