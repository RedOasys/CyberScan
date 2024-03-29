<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreAnalysesTable extends Migration
{
    public function up()
    {
        Schema::create('pre_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('static_analysis_id')->nullable();
            $table->json('data'); // A single JSON column to store all data

            $table->foreign('static_analysis_id')->references('id')->on('static_analyses'); // Set foreign key constraint


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pre_analyses');
    }
}
