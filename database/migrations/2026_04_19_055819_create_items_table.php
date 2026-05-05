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
        Schema::create('items', function (Blueprint $table) {
            $table->id();

            $table->string('kode_barang')->unique();

            // 🔥 RELASI KE CABINETS
            $table->foreignId('id_lemari')
                  ->constrained('cabinets')
                  ->cascadeOnDelete();

            $table->string('nama_barang');
            $table->string('kategori');
            $table->string('jenis_barang');
            $table->string('merk')->nullable();          
            $table->string('hasil_perolehan')->nullable();
            $table->integer('stok_awal');
            $table->integer('stok_total')->default(0);
            $table->integer('batas_minimum')->default(0);
            $table->bigInteger('harga')->default(0);
            $table->string('foto')->nullable();
                         $table->enum('kondisi', [
                'Baik',
                'Rusak Ringan',
                'Rusak Sedang',
                'Rusak Berat',
                'Mati Total',
            ])->default('Baik')->after('foto');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};