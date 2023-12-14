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
        Schema::create('dynamic_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_upload_id')->constrained()->onDelete('cascade');
            // Add fields specific to dynamic analysis
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_analyses');
    }
};
