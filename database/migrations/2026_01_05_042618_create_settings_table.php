<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('settings', function (Blueprint $table) {
        $table->id();
        $table->string('key')->unique(); // contoh: 'app_background'
        $table->text('value')->nullable();
        $table->timestamps();
    });
    
    // Seed data awal kosong
    DB::table('settings')->insert(['key' => 'app_background', 'value' => null]);
}
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
