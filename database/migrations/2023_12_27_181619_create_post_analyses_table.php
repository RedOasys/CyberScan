<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostAnalysesTable extends Migration
{
    public function up()
    {
        Schema::create('post_analyses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('static_analysis_id'); // Foreign key referencing 'id' in 'static_analyses'
            $table->json('data'); // A single JSON column to store all data

            $table->foreign('static_analysis_id')->references('id')->on('static_analyses'); // Set foreign key constraint


            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('post_analyses');
    }
}
