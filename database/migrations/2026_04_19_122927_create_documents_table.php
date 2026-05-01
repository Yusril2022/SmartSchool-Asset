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
    Schema::create('documents', function (Blueprint $table) {
        $table->id();

        $table->foreignId('uploaded_by')
              ->constrained('users')
              ->cascadeOnDelete();

        $table->foreignId('id_barang')
              ->nullable()
              ->constrained('items')
              ->nullOnDelete();

        $table->foreignId('id_peminjaman')
              ->nullable()
              ->constrained('borrowings')
              ->nullOnDelete();

        $table->string('judul_dokumen');
        $table->string('jenis_dokumen');
        $table->string('no_dokumen')->nullable();
        $table->date('tanggal_dokumen');
        $table->string('pihak_terkait')->nullable();
        $table->string('file_path')->nullable();
        $table->text('keterangan')->nullable();

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};