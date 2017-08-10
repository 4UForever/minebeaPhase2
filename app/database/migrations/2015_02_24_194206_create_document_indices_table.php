<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentIndicesTable extends Migration { 

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('document_indices', function($table) { 
      $table->increments('id');           
      $table->integer('document_id');
      $table->string('process_id');
      $table->integer('product_id');         
      $table->integer('line_id');        
      $table->timestamps();
                                                                                  
      $table->unique(array('document_id', 'process_id', 'product_id', 'line_id'), 'document_indices_unique');
      $table->index('document_id');
      $table->index('process_id');
      $table->index('product_id');
      $table->index('line_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('document_indices');
  }

}
