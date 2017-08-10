<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinesTable extends Migration {      

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('lines', function($table) { 
      $table->increments('id');             
      $table->string('title');         
      $table->timestamps();
                                
      $table->index('title');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('lines');
  }

}
