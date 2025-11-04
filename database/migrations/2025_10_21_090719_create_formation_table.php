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
        Schema::create('formation', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("formation_id_tanos");
            $table->string("nrp");
            $table->string("project_code");
            $table->bigInteger("regional_id");
            $table->bigInteger("unit_id");
            $table->bigInteger("job_id");
            $table->string("nama");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation');
    }
};
