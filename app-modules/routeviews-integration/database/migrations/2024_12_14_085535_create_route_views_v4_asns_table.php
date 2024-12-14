<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('route_views_v4_asns', function (Blueprint $table): void {
            $table->string('start_ip', 15);
            $table->string('end_ip', 15);
            $table->unsignedInteger('asn')->index();
            $table->string('organization')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('route_views_v4_asns');
    }
};
