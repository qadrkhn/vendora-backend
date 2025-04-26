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
        Schema::create('file_metadata', function (Blueprint $table) {
            $table->id();

            $table->string('original_name')->nullable();
            $table->string('visibility')->nullable();

            $table->string('mime_type')->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('type')->nullable();

            // might be needed in the future
            $table->json('custom')->nullable();

            $table->foreignId('file_id')->constrained('files')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_metadata');
    }
};
