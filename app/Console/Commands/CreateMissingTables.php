<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class CreateMissingTables extends Command
{
    protected $signature = 'tables:create';
    protected $description = 'Create missing payments and activities tables';

    public function handle()
    {
        $this->info('Creating missing tables...');
        
        try {
            // Create payments table
            if (!Schema::hasTable('payments')) {
                Schema::create('payments', function (Blueprint $table) {
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
                
                $this->info('✓ Payments table created');
            } else {
                $this->info('- Payments table already exists');
            }
            
            // Create activities table
            if (!Schema::hasTable('activities')) {
                Schema::create('activities', function (Blueprint $table) {
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
                
                $this->info('✓ Activities table created');
            } else {
                $this->info('- Activities table already exists');
            }
            
            // Show final status
            $this->info('\nFinal table status:');
            $tables = DB::select('SHOW TABLES');
            foreach($tables as $table) {
                $this->info('  - ' . $table->Tables_in_manfutsal);
            }
            
            $this->info('\nRecord counts:');
            $this->info('  Users: ' . DB::table('users')->count());
            $this->info('  Lapangan: ' . DB::table('lapangan')->count());
            $this->info('  Bookings: ' . DB::table('bookings')->count());
            $this->info('  Payments: ' . DB::table('payments')->count());
            $this->info('  Activities: ' . DB::table('activities')->count());
            
            $this->info('\n✅ Tables creation completed successfully!');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
