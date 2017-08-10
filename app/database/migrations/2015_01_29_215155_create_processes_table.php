<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessesTable extends Migration {      

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('processes', function($table) { 
      $table->increments('id');       
      $table->integer('line_id');  
      $table->string('number');  
      $table->string('title');         
      $table->timestamps();
                                
      $table->index('title');    
      $table->index('line_id');
      $table->unique('number');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('processes');
  }

}
