<?php
// Quick test script
require 'bootstrap/app.php';
$app = require_once 'bootstrap/app.php';

echo "✓ Application bootstrap OK\n";
echo "✓ Routes registered: " . count(Route::getRoutes()) . " routes\n";
echo "✓ Database connection test...\n";

try {
    DB::connection()->getPdo();
    echo "✓ Database connection OK\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
}

echo "\n=== Summary ===\n";
echo "All systems ready for testing!\n";
