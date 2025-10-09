<?php

/**
 * Test Runner Script for SIMPEG
 * 
 * Usage: php run_tests.php [options]
 * Options:
 *   --coverage     Generate coverage report
 *   --filter=      Run specific test filter
 *   --suite=       Run specific test suite (unit, feature, all)
 *   --verbose      Verbose output
 *   --quick        Skip slow tests
 */

$verbose = false;
$coverage = false;
$filter = null;
$suite = null;
$quick = false;

// Parse command line arguments
for ($i = 1; $i < $argc; $i++) {
    $arg = $argv[$i];
    
    if ($arg === '--coverage') {
        $coverage = true;
    } elseif (strpos($arg, '--filter=') === 0) {
        $filter = substr($arg, 9);
    } elseif (strpos($arg, '--suite=') === 0) {
        $suite = substr($arg, 8);
    } elseif ($arg === '--verbose') {
        $verbose = true;
    } elseif ($arg === '--quick') {
        $quick = true;
    }
}

// Build test command
$command = 'php artisan test';

// Add suite filter
if ($suite) {
    switch ($suite) {
        case 'unit':
            $command .= ' tests/Unit';
            break;
        case 'feature':
            $command .= ' tests/Feature';
            break;
        case 'notification':
            $command .= ' --filter=Notification';
            break;
        case 'cuti':
            $command .= ' --filter=Cuti';
            break;
        case 'perjalanan':
            $command .= ' --filter=Perjalanan';
            break;
        case 'laporan':
            $command .= ' --filter=Laporan';
            break;
    }
}

if ($filter) {
    $command .= " --filter=$filter";
}

if ($coverage) {
    $command .= ' --coverage --coverage-text';
}

if ($verbose) {
    $command .= ' --verbose';
}

if ($quick) {
    // Skip slow tests
    $command .= ' --exclude-group=slow';
}

// Add time limit for timeout
$command .= ' --max-performance-iterations=1';

echo "Running: $command\n";
echo "=================================\n";

// Execute the test
passthru($command, $exitCode);

exit($exitCode);
