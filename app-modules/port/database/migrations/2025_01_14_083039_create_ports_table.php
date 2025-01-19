<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('ports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ip_address_id')->index()->references('id')->on('ip_addresses')->cascadeOnDelete();
            $table->string('protocol');
            $table->unsignedSmallInteger('port')->index();
            $table->string('state');
            $table->timestamp('created_at', 6)->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
