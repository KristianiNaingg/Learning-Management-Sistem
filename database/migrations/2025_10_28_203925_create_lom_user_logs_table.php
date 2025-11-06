<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLomUserLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lom_user_logs', function (Blueprint $table) {
            $table->id();
            // Relasi ke user
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Polymorphic relation ke semua model LOM (Page, Quiz, Lesson, File, dll)
            $table->unsignedBigInteger('lom_id');
            $table->string('lom_type');

            // Detail aktivitas
            $table->string('action')->nullable(); // contoh: viewed, completed, downloaded
            $table->integer('duration')->nullable(); // lama waktu dalam detik
            $table->timestamp('accessed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lom_user_logs');
    }
}
