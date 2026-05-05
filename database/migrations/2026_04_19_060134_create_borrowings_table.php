<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();

            $table->string('kode_peminjaman')->unique();

            // 🔥 RELASI
            $table->foreignId('id_user')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('id_admin')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('id_barang')
                  ->constrained('items')
                  ->cascadeOnDelete();
            
            // 🔥 DATA
            $table->integer('jumlah_pinjam');
            $table->string('tujuan_pinjam')->nullable()->after('jumlah_pinjam');
            $table->string('status')->default('pending');

            $table->timestamp('tanggal_peminjaman');
            $table->timestamp('tanggal_kembali')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};