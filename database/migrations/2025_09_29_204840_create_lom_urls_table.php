<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lom_urls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subtopic_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_style_option_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('url_link'); // Menggunakan text untuk URL yang panjang
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('click_count')->default(0); // Menghitung jumlah klik
            $table->string('url_type')->nullable()->default('external'); // external, video, document, etc.
            $table->timestamps();

            // Indexes untuk performa
            $table->index('subtopic_id');
            $table->index('learning_style_option_id');
            $table->index('is_active');
            $table->index('url_type');
            $table->index('click_count');
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
        Schema::dropIfExists('lom_urls');
    }
}
