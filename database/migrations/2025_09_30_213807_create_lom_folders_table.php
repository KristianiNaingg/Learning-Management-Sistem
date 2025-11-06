<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomFoldersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lom_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('subtopic_id');
            $table->unsignedBigInteger('learning_style_option_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('subtopic_id')->references('id')->on('subtopics')->onDelete('cascade');
            $table->foreign('learning_style_option_id')->references('id')->on('learning_style_options')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lom_folders');
    }
}
