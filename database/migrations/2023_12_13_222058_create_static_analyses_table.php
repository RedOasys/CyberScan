<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaticAnalysesTable extends Migration
{
    public function up()
    {
        Schema::create('static_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_upload_id')->constrained()->onDelete('cascade');
            $table->string('analysis_id')->unique();
            $table->integer('score');
            $table->string('kind')->nullable();
            $table->string('state')->nullable();
            $table->string('media_type')->nullable();
            $table->string('md5')->nullable();
            $table->string('sha1')->nullable();
            $table->string('sha256')->nullable();
            // ... add more columns as needed ...
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('static_analyses');
    }
}
