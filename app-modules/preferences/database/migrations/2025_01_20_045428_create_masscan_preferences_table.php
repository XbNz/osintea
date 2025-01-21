<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('masscan_preferences', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->integer('rate')->default(10000);
            $table->string('adapter')->nullable();
            $table->unsignedInteger('retries')->default(0);
            $table->unsignedInteger('ttl')->default(55);
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masscan_preferences');
    }
};
