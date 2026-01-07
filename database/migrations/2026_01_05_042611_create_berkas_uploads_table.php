<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('berkas_uploads', function (Blueprint $table) {
        $table->id();
        $table->string('no_permohonan')->index(); // Indexing untuk pencarian cepat
        $table->string('nama_lengkap');
        $table->string('no_wa'); // Nanti kita format jadi 628xxx
        $table->string('lokasi_wawancara');
        $table->date('tanggal_foto');
        $table->string('nama_file_asli');
        $table->string('path_file');
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('berkas_uploads');
    }
};
