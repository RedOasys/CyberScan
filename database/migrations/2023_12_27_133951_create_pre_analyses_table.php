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
            $table->unsignedBigInteger('static_analysis_id');
            $table->string('pe_id_signatures')->nullable();
            $table->text('pe_imports')->nullable();
            $table->text('pe_sections')->nullable();
            $table->text('pe_resources')->nullable();
            $table->text('pe_version_info')->nullable();
            $table->string('pe_timestamp')->nullable();
            $table->text('signatures')->nullable();
            $table->text('errors')->nullable();
            $table->timestamps();

            $table->foreign('static_analysis_id')->references('id')->on('static_analyses');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pre_analyses');
    }
}
