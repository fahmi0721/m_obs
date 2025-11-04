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
        Schema::create('sop', function (Blueprint $table) {
            $table->id();
            $table->string("kode")->unique()->index();
            $table->string("nama");
            $table->string("deskripsi")->nullable();
            $table->string("file");
            $table->enum("status",array("valid","invalid"))->default("valid");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sop');
    }
};
