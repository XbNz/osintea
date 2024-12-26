<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('maxmind_v4_geolocations', function (Blueprint $table): void {
            $table->string('start_ip', 15);
            $table->string('end_ip', 15);
            $table->geometry('coordinates');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maxmind_v4_geolocations');
    }
};
