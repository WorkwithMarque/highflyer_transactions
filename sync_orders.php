<?php

// namespace App\Cron;

// // Load the Laravel application
// // require __DIR__ . '/../../bootstrap/app.php';

// // Set up the application instance
// $app = require_once __DIR__ . '/../../bootstrap/app.php';

// use Illuminate\Support\Facades\Artisan;

// Artisan::call('sales-orders:sync');

// echo "Sync completed.\n";
// <?php

// Load Laravel's autoloader and application

// Load Composer's autoloader
require __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';

// Bootstrap the Laravel console kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

try {
    // Run the Artisan command
    $kernel->handle(
        $input = new Symfony\Component\Console\Input\ArrayInput([
            'command' => 'sales-orders:sync',
        ]),
        new Symfony\Component\Console\Output\ConsoleOutput()
    );

    echo "Sales orders sync completed successfully.\n";
} catch (Exception $e) {
    // Handle errors gracefully
    echo "An error occurred while syncing sales orders: " . $e->getMessage() . "\n";
}
