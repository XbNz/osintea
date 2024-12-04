<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('ping_sequences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ip_address_id')->index()->references('id')->on('ip_addresses')->cascadeOnDelete();
            $table->float('round_trip_time')->nullable()->index();
            $table->boolean('loss');
            $table->timestamp('created_at', 6)->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ping_sequences');
    }
};
