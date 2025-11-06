<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('lom_labels', function (Blueprint $table) {
            $table->id();
            $table->text('content');
            $table->foreignId('subtopic_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_style_option_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0); // Untuk mengurutkan label
            $table->boolean('is_active')->default(true);
            $table->timestamps();

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
        Schema::dropIfExists('lom_labels');
    }
}
