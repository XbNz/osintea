<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('asns', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ip_address_id')->index()->references('id')->on('ip_addresses')->cascadeOnDelete();
            $table->string('organization');
            $table->integer('as_number');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asns');
    }
};
