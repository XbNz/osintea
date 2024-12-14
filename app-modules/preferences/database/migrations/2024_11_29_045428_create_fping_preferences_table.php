<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('fping_preferences', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('size')->default(56);
            $table->float('backoff')->default(1.5);
            $table->unsignedInteger('count')->default(1);
            $table->unsignedInteger('ttl')->default(64);
            $table->unsignedInteger('interval')->default(10);
            $table->unsignedInteger('interval_per_target')->default(1000);
            $table->string('type_of_service')->default('0x00');
            $table->unsignedInteger('retries')->default(0);
            $table->unsignedInteger('timeout')->default(500);
            $table->boolean('dont_fragment')->default(false);
            $table->boolean('send_random_data')->default(false);
            $table->boolean('enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fping_preferences');
    }
};
