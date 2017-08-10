<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLastLogoutToUserTable extends Migration {       

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('users', function($table) {
      $table->timestamp('last_logout')->index()->after('last_login');  
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() { 
    Schema::table('users', function($table) {
      $table->dropColumn('last_logout');            
    });
  }

}
