<?php
/**
 * Composer post-install script to extract obfuscated code from code.zip
 * 
 * This script:
 * 1. Extracts code.zip if it exists
 * 2. Copies contents from the 'code' folder inside the zip to 'src' directory
 * 3. Cleans up temporary files
 */

$zipFile = __DIR__ . '/../code.zip';
$srcDir = __DIR__ . '/../src';
$tempExtractDir = sys_get_temp_dir() . '/uploader-extract-' . uniqid();

// Clean up temporary directory helper function
function removeDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    
    $files = array_diff(scandir($dir), ['.', '..']);
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        is_dir($path) ? removeDirectory($path) : unlink($path);
    }
    @rmdir($dir);
}

if (!file_exists($zipFile)) {
    echo "Info: code.zip not found, skipping extraction.\n";
    exit(0);
}

if (!extension_loaded('zip')) {
    echo "Error: Zip extension is not loaded. Please install php-zip extension.\n";
    exit(1);
}

echo "Extracting code.zip...\n";

$zip = new ZipArchive();
$result = $zip->open($zipFile);

if ($result !== true) {
    echo "Error: Failed to open code.zip (error code: $result)\n";
    exit(1);
}

// Extract to temporary directory
if (!is_dir($tempExtractDir)) {
    mkdir($tempExtractDir, 0755, true);
}

$zip->extractTo($tempExtractDir);
$zip->close();

$codeDir = $tempExtractDir . '/code';

if (!is_dir($codeDir)) {
    echo "Error: 'code' folder not found inside code.zip\n";
    removeDirectory($tempExtractDir);
    exit(1);
}

echo "Copying files from code/ to src/...\n";

// Ensure src directory exists
if (!is_dir($srcDir)) {
    mkdir($srcDir, 0755, true);
}

// Copy files recursively
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($codeDir, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);

foreach ($iterator as $item) {
    $targetPath = $srcDir . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
    
    if ($item->isDir()) {
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
    } else {
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        copy($item->getPathname(), $targetPath);
    }
}

// Clean up temporary directory
removeDirectory($tempExtractDir);

echo "Successfully extracted and copied obfuscated code to src/\n";

