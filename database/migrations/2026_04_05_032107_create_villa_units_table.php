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
        Schema::create('villa_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->string('unit_name');
            $table->enum('status', ['occupied', 'available', 'cleaning', 'maintenance'])->default('available');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villa_units');
    }
};
