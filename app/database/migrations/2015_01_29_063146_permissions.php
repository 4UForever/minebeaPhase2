<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Permissions extends Migration {     

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::create('permissions', function($table) { 
        $table->increments('id');  
        $table->string('title');       
        $table->string('key');       
        $table->timestamps();
                                  
        $table->index('title');
        $table->index('key');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() {
    Schema::drop('permissions');
  }

}
