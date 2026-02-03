<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create log file
$logFile = fopen('migration_log.txt', 'w');
fwrite($logFile, "=== Migration Log ===\n\n");

try {
    fwrite($logFile, "1. Checking database connection...\n");
    $pdo = DB::connection()->getPdo();
    fwrite($logFile, "   ✓ Connected\n\n");
    
    fwrite($logFile, "2. Checking existing tables...\n");
    $tables = DB::select('SHOW TABLES');
    foreach($tables as $table) {
        fwrite($logFile, "   - " . $table->Tables_in_manfutsal . "\n");
    }
    
    fwrite($logFile, "\n3. Creating payments table if not exists...\n");
    if (!Schema::hasTable('payments')) {
        Schema::create('payments', function ($table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->decimal('jumlah', 10, 2);
            $table->string('metode_pembayaran');
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
            
            $table->index('booking_id');
            $table->index('status');
        });
        
        Schema::table('payments', function (Blueprint $table) {
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
        });
        
        fwrite($logFile, "   ✓ Payments table created\n");
    } else {
        fwrite($logFile, "   - Payments table already exists\n");
    }
    
    fwrite($logFile, "\n4. Creating activities table if not exists...\n");
    if (!Schema::hasTable('activities')) {
        Schema::create('activities', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('action');
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('action');
            $table->index('created_at');
        });
        
        Schema::table('activities', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        
        fwrite($logFile, "   ✓ Activities table created\n");
    } else {
        fwrite($logFile, "   - Activities table already exists\n");
    }
    
    fwrite($logFile, "\n5. Final check:\n");
    $finalTables = DB::select('SHOW TABLES');
    foreach($finalTables as $table) {
        fwrite($logFile, "   - " . $table->Tables_in_manfutsal . "\n");
    }
    
    fwrite($logFile, "\n6. Record counts:\n");
    fwrite($logFile, "   Users: " . DB::table('users')->count() . "\n");
    fwrite($logFile, "   Lapangan: " . DB::table('lapangan')->count() . "\n");
    fwrite($logFile, "   Bookings: " . DB::table('bookings')->count() . "\n");
    fwrite($logFile, "   Payments: " . DB::table('payments')->count() . "\n");
    fwrite($logFile, "   Activities: " . DB::table('activities')->count() . "\n");
    
    fwrite($logFile, "\n=== Migration Complete ===\n");
    
} catch(Exception $e) {
    fwrite($logFile, "ERROR: " . $e->getMessage() . "\n");
    fwrite($logFile, "Stack: " . $e->getTraceAsString() . "\n");
}

fclose($logFile);
echo "Migration completed. Check migration_log.txt for results.";
?>
