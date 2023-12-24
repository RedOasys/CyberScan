<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_upload_id')->constrained('file_uploads');
            $table->foreignId('analysis_id')->constrained('static_analyses');
            $table->boolean('detected');
            $table->string('malware_type');
            $table->unsignedSmallInteger('certainty')->comment('Certainty as a percentage')->nullable();
            $table->string('source');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('detections');
    }
}
