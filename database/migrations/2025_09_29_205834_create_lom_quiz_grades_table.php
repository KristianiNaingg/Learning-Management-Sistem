<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomQuizGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('lom_quiz_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lom_quizzes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('attempt_id')->constrained('lom_quiz_attempts')->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->decimal('grade', 5, 2); // Nilai grade dengan 2 desimal
            $table->decimal('total_points', 8, 2)->default(0); // Total poin yang didapat
            $table->decimal('max_points', 8, 2)->default(0); // Total poin maksimal
            $table->timestamp('completed_at')->nullable();
            $table->text('feedback')->nullable(); // Feedback dari pengajar
            $table->boolean('is_passed')->default(false); // Status lulus/tidak
            $table->timestamps();

            // Indexes untuk performa
            $table->index('quiz_id');
            $table->index('user_id');
            $table->index('attempt_id');
            $table->index('attempt_number');
            $table->index('completed_at');
            $table->index('is_passed');
            $table->index('created_at');

            // Unique constraint - satu grade per attempt
            $table->unique(['attempt_id']);
            
            // Unique constraint - mencegah duplikasi grade untuk user dan quiz yang sama dengan attempt_number yang sama
            $table->unique(['quiz_id', 'user_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lom_quiz_grades');
    }
}
