<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('visitor_counters', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('adult_count')->default(0);
            $table->integer('teenager_count')->default(0);
            $table->integer('child_count')->default(0);
            $table->boolean('is_group')->default(false);
            $table->text('notes')->nullable();
            $table->foreignId('cashier_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_counters');
    }
};
