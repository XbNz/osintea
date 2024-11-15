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
        Schema::create('ip_addresses', function (Blueprint $table): void {
            $table->id();
            $table->string('ip', 39)->index()->unique();
            $table->tinyInteger('type')->virtualAs(DB::raw('CASE WHEN LENGTH(ip) > 19 THEN 6 ELSE 4 END'));
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ip_addresses');
    }
};
