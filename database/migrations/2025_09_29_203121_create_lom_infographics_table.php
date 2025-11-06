<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomInfographicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('lom_infographics', function (Blueprint $table) {
            $table->id();
            $table->string('file_path'); // Path penyimpanan file infografis
            $table->string('title')->nullable(); // Opsional: judul infografis
            $table->text('description')->nullable(); // Opsional: deskripsi
            $table->foreignId('subtopic_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_style_option_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->integer('view_count')->default(0);
            $table->timestamps();

            // Indexes untuk performa
            $table->index('subtopic_id');
            $table->index('learning_style_option_id');
            $table->index('is_active');
            $table->index('view_count');
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
        Schema::dropIfExists('lom_infographics');
    }
}
