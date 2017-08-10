<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentsTable extends Migration {    

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('documents', function($table) { 
      $table->increments('id');  
      $table->integer('document_category_id');  
      $table->string('title');          
      $table->string('file_path'); 
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
    Schema::drop('documents');
  }

}
