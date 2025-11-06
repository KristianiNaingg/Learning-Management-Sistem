<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomAssignfeedbackCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lom_assignfeedback_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('lom_assign_submissions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->timestamp('created_at')->useCurrent(); // Manual timestamp karena $timestamps = false

            // Indexes untuk performa
            $table->index('submission_id');
            $table->index('user_id');
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
        Schema::dropIfExists('lom_assignfeedback_comments');
    }
}
