<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Add payment_status column
            $table->enum('payment_status', ['unpaid', 'dp_paid', 'paid', 'refunded'])
                ->default('unpaid')
                ->after('payment_method');
        });

        // Migrate existing data: confirmed → booked + dp_paid
        DB::table('reservations')->where('status', 'confirmed')->update([
            'status' => 'booked',
            'payment_status' => 'dp_paid',
        ]);

        // Change status enum values
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending','booked','checked_in','checked_out','cancelled') DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Revert status enum
        DB::table('reservations')->where('status', 'booked')->update(['status' => 'confirmed']);
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending'");

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
