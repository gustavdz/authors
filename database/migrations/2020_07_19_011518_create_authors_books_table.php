<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorsBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors_books', function (Blueprint $table) {
            $table->bigIncrements('author_book_id');
            $table->bigInteger('author_id')->unsigned();
            $table->bigInteger('book_id')->unsigned();
            $table->string('name');

            $table->foreign('author_id')->references('author_id')->on('authors')->onDelete('cascade');
            $table->foreign('book_id')->references('book_id')->on('books')->onDelete('cascade');

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
        Schema::dropIfExists('authors_books');
    }
}
