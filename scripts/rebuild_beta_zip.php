<?php

$betaDir = realpath(__DIR__ . '/../beta');
$zipFile = $betaDir . '/beta.zip';

if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Cannot create {$zipFile}\n");
}

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($betaDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

$count = 0;
foreach ($files as $file) {
    $filePath = $file->getRealPath();
    if ($file->getFilename() === 'beta.zip' || $filePath === realpath($zipFile)) {
        continue;
    }

    $relativePath = substr($filePath, strlen($betaDir) + 1);
    $relativePath = str_replace('\\', '/', $relativePath);

    if ($file->isDir()) {
        $zip->addEmptyDir($relativePath);
    } elseif ($file->isFile()) {
        $zip->addFile($filePath, $relativePath);
        $count++;
    }
}

$zip->close();
echo "SUCCESS: Created beta.zip with {$count} files (" . round(filesize($zipFile) / (1024 * 1024), 2) . " MB)\n";
