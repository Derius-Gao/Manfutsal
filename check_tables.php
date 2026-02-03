<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== ManFutsal Database Check ===\n\n";

try {
    // Check database connection
    echo "1. Testing database connection...\n";
    DB::connection()->getPdo();
    echo "   ✓ Database connection successful\n\n";
    
    // Check existing tables
    echo "2. Checking existing tables...\n";
    $tables = DB::select('SHOW TABLES');
    $existingTables = [];
    foreach($tables as $table) {
        $tableName = $table->Tables_in_manfutsal;
        $existingTables[] = $tableName;
        echo "   - $tableName\n";
    }
    
    echo "\n3. Checking required tables...\n";
    $requiredTables = ['users', 'lapangan', 'bookings', 'payments', 'activities'];
    
    foreach($requiredTables as $table) {
        if (in_array($table, $existingTables)) {
            echo "   ✓ $table exists\n";
        } else {
            echo "   ✗ $table MISSING\n";
        }
    }
    
    // Try to create missing tables
    if (!in_array('payments', $existingTables)) {
        echo "\n4. Creating payments table...\n";
        try {
            Schema::create('payments', function ($table) {
                $table->id();
                $table->foreignId('booking_id')->constrained()->onDelete('cascade');
                $table->decimal('jumlah', 10, 2);
                $table->string('metode_pembayaran');
                $table->string('bukti_pembayaran')->nullable();
                $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
                $table->text('catatan')->nullable();
                $table->timestamps();
                
                $table->index('booking_id');
                $table->index('status');
            });
            echo "   ✓ Payments table created successfully\n";
        } catch(Exception $e) {
            echo "   ✗ Error creating payments table: " . $e->getMessage() . "\n";
        }
    }
    
    if (!in_array('activities', $existingTables)) {
        echo "\n5. Creating activities table...\n";
        try {
            Schema::create('activities', function ($table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('action');
                $table->text('description');
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();
                
                $table->index('user_id');
                $table->index('action');
                $table->index('created_at');
            });
            echo "   ✓ Activities table created successfully\n";
        } catch(Exception $e) {
            echo "   ✗ Error creating activities table: " . $e->getMessage() . "\n";
        }
    }
    
    // Final check
    echo "\n6. Final table status:\n";
    $finalTables = DB::select('SHOW TABLES');
    foreach($finalTables as $table) {
        echo "   - " . $table->Tables_in_manfutsal . "\n";
    }
    
    echo "\n7. Record counts:\n";
    echo "   Users: " . DB::table('users')->count() . "\n";
    echo "   Lapangan: " . DB::table('lapangan')->count() . "\n";
    echo "   Bookings: " . DB::table('bookings')->count() . "\n";
    echo "   Payments: " . DB::table('payments')->count() . "\n";
    echo "   Activities: " . DB::table('activities')->count() . "\n";
    
    echo "\n=== Database Check Complete ===\n";
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
