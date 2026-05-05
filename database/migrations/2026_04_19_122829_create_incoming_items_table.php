<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incoming_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('items')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete();
            $table->integer('jumlah_masuk');
            $table->timestamp('tanggal_masuk');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incoming_items');
    }
};