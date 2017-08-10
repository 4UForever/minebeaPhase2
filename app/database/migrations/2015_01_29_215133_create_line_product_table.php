<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLineProductTable extends Migration {    

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('line_product', function($table) { 
      $table->increments('id');  
      $table->integer('line_id');         
      $table->integer('product_id');         
      $table->timestamps();
                                
      $table->unique(array('line_id', 'product_id'));
      $table->index('line_id');
      $table->index('product_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('line_product');
  }


}
