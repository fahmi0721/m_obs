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
        Schema::create('employee', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("employee_id_tanos");
            $table->string("nrp");
            $table->string("nama");
            $table->string("alamat")->nullable();
            $table->string("umur");
            $table->string("email")->nullable();;
            $table->string("gender");
            $table->string("status");
            $table->string("religion");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee');
    }
};
