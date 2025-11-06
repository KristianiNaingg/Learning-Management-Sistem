<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('lom_lessons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('subtopic_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_style_option_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0); // Untuk mengurutkan lesson
            $table->boolean('is_active')->default(true);
            $table->integer('duration_minutes')->nullable(); // Durasi lesson dalam menit
            $table->timestamp('created_at')->useCurrent(); // Manual timestamp karena $timestamps = false

            // Indexes untuk performa
            $table->index('subtopic_id');
            $table->index('learning_style_option_id');
            $table->index('order');
            $table->index('is_active');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lom_lessons');
    }
}
