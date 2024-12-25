<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::create('maxmind_v4_geolocations', function (Blueprint $table): void {
            $table->string('start_ip');
            $table->string('end_ip');
            $table->binary('coordinates');
        });

        DB::statement('SELECT AddGeometryColumn("maxmind_v4_geolocations", "coordinates", 4326, "POINT", "XY")');
    }

    public function down(): void
    {
        Schema::dropIfExists('maxmind_v4_geolocations');
    }
};
