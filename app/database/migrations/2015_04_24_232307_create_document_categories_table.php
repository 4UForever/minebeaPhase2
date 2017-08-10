<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentCategoriesTable extends Migration {    

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('document_categories', function($table) { 
      $table->increments('id');                 
      $table->string('title');
      $table->timestamps();
                                                                  
      $table->unique('title');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('document_categories');
  }

}
