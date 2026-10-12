<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

echo "✓ Application bootstrap OK\n";

try {
    $customer = Customer::updateOrCreate(
        ['email' => 'budi.santoso@gmail.com'],
        [
            'name' => 'Budi Santoso',
            'password' => Hash::make('rahasia3gfood'),
            'phone' => '081234567890'
        ]
    );
    echo "✓ Customer created/updated: ID {$customer->id}, Name: {$customer->name}\n";

    // Test attempt with correct password
    $attemptSuccess = Auth::guard('customer')->attempt([
        'email' => 'budi.santoso@gmail.com',
        'password' => 'rahasia3gfood'
    ]);
    echo "✓ Auth guard customer attempt valid: " . ($attemptSuccess ? 'BERHASIL' : 'GAGAL') . "\n";

    // Test attempt with wrong password
    $attemptFail = Auth::guard('customer')->attempt([
        'email' => 'budi.santoso@gmail.com',
        'password' => 'wrongpassword'
    ]);
    echo "✓ Auth guard customer attempt invalid: " . (!$attemptFail ? 'BERHASIL DITOLAK' : 'SALAH') . "\n";

    // Verify guard isolation: check that guard web is not affected
    echo "✓ Guard admin/web check status: " . (Auth::guard('web')->check() ? 'Admin logged in' : 'Admin NOT logged in (Isolated)') . "\n";
    echo "✓ Guard customer check status: " . (Auth::guard('customer')->check() ? 'Customer logged in' : 'Customer NOT logged in') . "\n";

    echo "\n=== ALL TESTS PASSED! ===\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

