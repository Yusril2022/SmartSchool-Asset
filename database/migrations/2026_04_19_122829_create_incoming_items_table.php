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
        Schema::create('incoming_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_barang')->constrained('items')->cascadeOnDelete();
            $table->foreignId('id_user')->nullable()->change();
            $table->string('nama_pengambil')->nullable()->after('id_user');
            $table->integer('jumlah_masuk');
            $table->timestamp('tanggal_masuk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
public function down(): void
    {
        Schema::table('item_usages', function (Blueprint $table) {
            $table->dropColumn('nama_pengambil');
            $table->foreignId('id_user')->nullable(false)->change();
        });
    }
};