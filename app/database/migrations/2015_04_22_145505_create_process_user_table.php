<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessUserTable extends Migration {  

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('process_user', function($table) { 
      $table->increments('id');           
      $table->integer('process_id');
      $table->integer('user_id');         
      $table->timestamps();
                                
      $table->unique(array('process_id', 'user_id'));
      $table->index('process_id');
      $table->index('user_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('process_user');
  }

}
