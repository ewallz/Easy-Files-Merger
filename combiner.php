<?php

// Define the root directory and the output file
$rootDirectory = __DIR__; // Adjust this if your script is not in the project root
$outputFile = "$rootDirectory/combined.txt";

// Specify the file extensions to include
$allowedExtensions = ['php', 'js', 'css', 'html', 'json', 'lock'];

// Specify the directories and filenames to ignore
$ignoredDirectories = ['vendor', 'node_modules', 'storage', 'bootstrap/cache', '.git', 'tests'];
$ignoredFilenames = ['combiner.php', 'run.php', 'combined.txt'];

// Remove time and memory limits
set_time_limit(0);
ini_set('memory_limit', '-1');

// Open the output file for writing
$outputHandle = fopen($outputFile, 'w');
if (!$outputHandle) {
    die("Failed to open output file for writing.");
}

// Function to generate a directory tree
function generateDirectoryTree($directory, $ignoredDirectories, $ignoredFilenames, $rootDirectory, $prefix = '')
{
    $files = scandir($directory);
    $tree = '';

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $directory . DIRECTORY_SEPARATOR . $file;

        // Skip ignored directories
        $relativePath = str_replace($rootDirectory . DIRECTORY_SEPARATOR, '', $filePath);
        foreach ($ignoredDirectories as $ignoredDirectory) {
            if (strpos($relativePath, $ignoredDirectory) === 0) {
                continue 2;
            }
        }

        // Skip ignored filenames
        if (in_array($file, $ignoredFilenames)) {
            continue;
        }

        if (is_dir($filePath)) {
            $tree .= "$prefix+-- $file\n";
            $tree .= generateDirectoryTree($filePath, $ignoredDirectories, $ignoredFilenames, $rootDirectory, $prefix . '|   ');
        } else {
            $tree .= "$prefix+-- $file\n";
        }
    }

    return $tree;
}

// Function to count total files to be processed
function countFiles($directory, $allowedExtensions, $ignoredDirectories, $ignoredFilenames, $rootDirectory)
{
    $files = scandir($directory);
    $count = 0;

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $directory . DIRECTORY_SEPARATOR . $file;

        // Skip ignored directories
        $relativePath = str_replace($rootDirectory . DIRECTORY_SEPARATOR, '', $filePath);
        foreach ($ignoredDirectories as $ignoredDirectory) {
            if (strpos($relativePath, $ignoredDirectory) === 0) {
                continue 2;
            }
        }

        // Skip ignored filenames
        if (in_array($file, $ignoredFilenames)) {
            continue;
        }

        if (is_dir($filePath)) {
            $count += countFiles($filePath, $allowedExtensions, $ignoredDirectories, $ignoredFilenames, $rootDirectory);
        } elseif (is_file($filePath)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (in_array($extension, $allowedExtensions)) {
                $count++;
            }
        }
    }

    return $count;
}

// Recursive function to process files and directories
function processDirectory($directory, $allowedExtensions, $outputHandle, $rootDirectory, $ignoredDirectories, $ignoredFilenames, &$processed, $totalFiles)
{
    $files = scandir($directory);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $filePath = $directory . DIRECTORY_SEPARATOR . $file;

        // Skip ignored directories
        $relativePath = str_replace($rootDirectory . DIRECTORY_SEPARATOR, '', $filePath);
        foreach ($ignoredDirectories as $ignoredDirectory) {
            if (strpos($relativePath, $ignoredDirectory) === 0) {
                continue 2;
            }
        }

        // Skip ignored filenames
        if (in_array($file, $ignoredFilenames)) {
            continue;
        }

        if (is_dir($filePath)) {
            // Recurse into subdirectories
            processDirectory($filePath, $allowedExtensions, $outputHandle, $rootDirectory, $ignoredDirectories, $ignoredFilenames, $processed, $totalFiles);
        } elseif (is_file($filePath)) {
            $extension = pathinfo($filePath, PATHINFO_EXTENSION);
            if (in_array($extension, $allowedExtensions)) {
                $processed++;
                $progress = round(($processed / $totalFiles) * 100, 2);
                echo "Progress: $progress%\n";

                // Write header
                $relativePath = str_replace($rootDirectory, '', $filePath);
                fwrite($outputHandle, "-----------------
");
                fwrite($outputHandle, "Filename:
$relativePath
");
                fwrite($outputHandle, "Content:
-----------------
");

                // Write file content
                $content = file_get_contents($filePath);
                fwrite($outputHandle, $content . "\n");
                fwrite($outputHandle, "\n-----------------
\n");
            }
        }
    }
}

// Generate directory tree
$directoryTree = generateDirectoryTree($rootDirectory, $ignoredDirectories, $ignoredFilenames, $rootDirectory);

// Write directory tree to the output file
fwrite($outputHandle, "Directory Tree:\n-----------------
");
fwrite($outputHandle, $directoryTree . "\n");

// Count total files to process
$totalFiles = countFiles($rootDirectory, $allowedExtensions, $ignoredDirectories, $ignoredFilenames, $rootDirectory);
echo "Total files to process: $totalFiles\n";

// Initialize processed files counter
$processed = 0;

// Start processing from the root directory
processDirectory($rootDirectory, $allowedExtensions, $outputHandle, $rootDirectory, $ignoredDirectories, $ignoredFilenames, $processed, $totalFiles);

// Close the output file
fclose($outputHandle);

echo "Combined file created at: $outputFile\n";
