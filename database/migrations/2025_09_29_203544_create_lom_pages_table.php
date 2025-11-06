<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('lom_pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('content'); // Menggunakan longText untuk konten yang panjang
            $table->foreignId('subtopic_id')->constrained()->onDelete('cascade');
            $table->foreignId('learning_style_option_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique(); // Untuk URL yang SEO-friendly
            $table->integer('order')->default(0); // Untuk mengurutkan halaman
            $table->boolean('is_active')->default(true);
            $table->boolean('is_published')->default(false);
            $table->integer('view_count')->default(0);
            $table->timestamps();

            // Indexes untuk performa
            $table->index('subtopic_id');
            $table->index('learning_style_option_id');
            $table->index('slug');
            $table->index('order');
            $table->index('is_active');
            $table->index('is_published');
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
        Schema::dropIfExists('lom_pages');
    }
}
