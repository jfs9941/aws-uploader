<?php
/**
 * Composer post-install script to extract obfuscated code from code.zip
 * 
 * This script:
 * 1. Extracts code.zip if it exists
 * 2. Copies contents from the 'code' folder inside the zip to 'src' directory
 * 3. Cleans up temporary files
 */

// Check for code.zip in root first, then in src directory
$projectRoot = __DIR__ . '/..';
$zipFile = file_exists($projectRoot . '/code.zip') 
    ? $projectRoot . '/code.zip' 
    : ($projectRoot . '/src/code.zip');
$srcDir = $projectRoot . '/src';
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
    echo "Info: code.zip not found at: $zipFile\n";
    echo "Info: Skipping extraction.\n";
    exit(0);
}

echo "Found code.zip at: $zipFile\n";

if (!extension_loaded('zip')) {
    echo "Error: Zip extension is not loaded. Please install php-zip extension.\n";
    exit(1);
}


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

// Check if there's a 'code' folder, otherwise use root of extracted zip
$codeDir = $tempExtractDir . '/code';
if (!is_dir($codeDir)) {
    // No 'code' folder, use root of extracted zip
    $codeDir = $tempExtractDir;
    
    // Check if we have any PHP files in the root
    $hasPhpFiles = false;
    $items = scandir($codeDir);
    foreach ($items as $item) {
        if ($item !== '.' && $item !== '..' && (is_file($codeDir . '/' . $item) || is_dir($codeDir . '/' . $item))) {
            $hasPhpFiles = true;
            break;
        }
    }
    
    if (!$hasPhpFiles) {
        echo "Error: No valid content found in code.zip\n";
        removeDirectory($tempExtractDir);
        exit(1);
    }
}

$fileCount = 0;
$dirCount = 0;

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
    $subPathName = $iterator->getSubPathName();
    
    // Skip macOS metadata files
    if (strpos($subPathName, '__MACOSX') !== false || strpos($subPathName, '._') === 0) {
        continue;
    }
    
    $targetPath = $srcDir . DIRECTORY_SEPARATOR . $subPathName;
    
    if ($item->isDir()) {
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
            $dirCount++;
        }
    } else {
        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        copy($item->getPathname(), $targetPath);
        $fileCount++;
    }
}

// Clean up temporary directory
removeDirectory($tempExtractDir);

echo "✅ Successfully deploy code/\n";
echo "   📁 Created {$dirCount} directories\n";
echo "   📄 Copied {$fileCount} files\n";
echo "   📍 Target directory: {$srcDir}\n";

// Verify extraction by checking for PHP files in key directories
$keyDirs = ['Providers', 'Service', 'Core'];
$foundDirs = 0;
$totalPhpFiles = 0;

foreach ($keyDirs as $dir) {
    $dirPath = $srcDir . '/' . $dir;
    if (is_dir($dirPath)) {
        $foundDirs++;
        $phpFiles = glob($dirPath . '/*.php');
        $totalPhpFiles += count($phpFiles);
    }
}

if ($foundDirs > 0 && $totalPhpFiles > 0) {
    echo "   ✓ Verified: {$foundDirs}/" . count($keyDirs) . " key directories found\n";
    echo "   ✓ Verified: {$totalPhpFiles} PHP files extracted\n";
} else {
    echo "   ⚠ Warning: Extraction verification failed\n";
}

