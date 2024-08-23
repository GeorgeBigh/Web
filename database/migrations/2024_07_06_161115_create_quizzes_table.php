<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuizzesTable extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('quizz_time', 8, 2)->nullable();
            // $table->unsignedBigInteger('company_id')->nullable();
            // $table->integer('assigned_user_id')->nullable();
            $table->timestamps();

            // Foreign key constraint
            // $table->foreign('company_id')
            //       ->references('id')->on('companies')
            //       ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quizzes');
    }
}
