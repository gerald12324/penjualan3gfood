<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_toko', function (Blueprint $table) {
            $table->id();
            $table->enum('status_toko', ['buka', 'tutup'])->default('buka');
            $table->string('jam_buka')->default('08:00');
            $table->string('jam_tutup')->default('21:00');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_toko');
    }
};
