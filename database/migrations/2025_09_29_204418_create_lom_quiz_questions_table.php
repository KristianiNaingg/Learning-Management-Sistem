<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomQuizQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lom_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lom_quizzes')->onDelete('cascade');
            $table->text('question_text');
            $table->text('options_a')->nullable();
            $table->text('options_b')->nullable();
            $table->text('options_c')->nullable();
            $table->text('options_d')->nullable();
            $table->enum('correct_answer', ['a', 'b', 'c', 'd'])->nullable();
            $table->decimal('point', 5, 2)->default(1.00);
            $table->integer('order')->default(0);
            $table->text('explanation')->nullable();
            $table->timestamps();

            // Indexes untuk performa
            $table->index('quiz_id');
            $table->index('order');
            $table->index('correct_answer');
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
        Schema::dropIfExists('lom_quiz_questions');
    }
}
