<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomQuizzesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lom_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtopic_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_dimension_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable(); // Instruksi tambahan untuk quiz
            $table->timestamp('time_open')->nullable(); // Waktu buka quiz
            $table->timestamp('time_close')->nullable(); // Waktu tutup quiz
            $table->integer('time_limit')->nullable(); // Batas waktu dalam menit
            $table->integer('max_attempts')->default(1); // Maksimal percobaan
            $table->decimal('grade_to_pass', 5, 2)->default(70.00); // Nilai minimal untuk lulus
            $table->decimal('max_grade', 5, 2)->default(100.00); // Nilai maksimal
            $table->boolean('shuffle_questions')->default(false); // Acak soal
            $table->boolean('show_correct_answers')->default(false); // Tampilkan jawaban benar
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes untuk performa
            $table->index('subtopic_id');
            $table->index('learning_dimension_id');
            $table->index('time_open');
            $table->index('time_close');
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
        Schema::dropIfExists('lom_quizzes');
    }
}
