<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher', function (Blueprint $table) {
            $table->id();
            $table->string('kode_voucher')->unique();
            $table->enum('jenis_potongan', ['nominal', 'persen'])->default('nominal');
            $table->unsignedInteger('nilai_potongan')->default(0);
            $table->unsignedInteger('min_belanja')->default(0);
            $table->date('tanggal_berakhir')->nullable();
            $table->unsignedInteger('kuota')->default(0);
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher');
    }
};
