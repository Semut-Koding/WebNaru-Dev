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
        Schema::create('attraction_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attraction_id')->constrained('attractions')->onDelete('cascade');
            $table->date('date');
            $table->integer('count')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('attraction_operator_id')->constrained('users')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attraction_counters');
    }
};
