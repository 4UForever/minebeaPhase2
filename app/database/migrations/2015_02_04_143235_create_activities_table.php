<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActivitiesTable extends Migration {       

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('activities', function($table) { 
      $table->increments('id');  
      $table->integer('type_id');  
      $table->string('type_title');  
      $table->integer('user_id');     
      $table->string('user_full_name');     
      $table->string('user_email');
      $table->integer('line_id');  
      $table->string('line_title');  
      $table->integer('process_id');
      $table->string('process_title');
      $table->integer('product_id');  
      $table->string('product_title');  
      $table->text('comment')->nullable();
      $table->timestamps();

      $table->index('type_id');
      $table->index('type_title');
      $table->index('user_id');      
      $table->index('user_full_name');      
      $table->index('user_email');      
      $table->index('line_id');
      $table->index('line_title');
      $table->index('process_id');
      $table->index('process_title');
      $table->index('product_id');
      $table->index('product_title');
      $table->index('created_at');
      $table->index('updated_at');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('activities');
  }

}
