<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pencairan_kurir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('courier_id')->constrained('couriers')->cascadeOnDelete();
            $table->unsignedInteger('jumlah_antaran')->default(0);
            $table->unsignedInteger('tarif_per_antaran')->default(2000);
            $table->unsignedInteger('total_pencairan')->default(0);
            $table->string('periode');
            $table->enum('status', ['request', 'dibayar'])->default('request');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pencairan_kurir');
    }
};
