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
        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('bedroom_count')->default(1);
            $table->integer('bathroom_count')->default(1);
            $table->integer('capacity')->default(2);
            $table->json('amenities')->nullable();
            $table->json('benefits')->nullable();
            $table->decimal('base_price_weekday', 12, 2)->default(0);
            $table->decimal('base_price_weekend', 12, 2)->default(0);
            $table->enum('status', ['available', 'coming_soon', 'closed'])->default('available');
            $table->integer('sort_order')->default(0);
            $table->geometry('coordinate')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('villas');
    }
};
