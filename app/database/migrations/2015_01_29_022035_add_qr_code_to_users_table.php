<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQrCodeToUsersTable extends Migration {       

  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up() {
    Schema::table('users', function($table) {
      $table->string('qr_code')->unique()->after('password');  
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down() { 
    Schema::table('users', function($table) {
      $table->dropColumn('qr_code');            
    });
  }

}
