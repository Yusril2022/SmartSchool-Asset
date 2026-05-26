<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_condition_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_barang')
                  ->constrained('items')
                  ->cascadeOnDelete();

            $table->foreignId('id_admin')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->string('kondisi_lama')->nullable();
            $table->string('kondisi_baru');
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_condition_logs');
    }
};