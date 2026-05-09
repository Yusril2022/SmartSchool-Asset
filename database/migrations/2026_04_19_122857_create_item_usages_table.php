<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_usages', function (Blueprint $table) {
            $table->id();
            $table->uuid('session_id')->index();
            $table->foreignId('id_barang')->constrained('items')->cascadeOnDelete();
            $table->foreignId('id_user')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pengambil')->nullable();
            $table->enum('sebagai', ['murid', 'pegawai'])->nullable();
            $table->integer('jumlah_ambil');
            $table->timestamp('tanggal_ambil');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_usages');
    }
};