<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class ProcessLogNg1 extends Ardent {

  // --------------------------------------------------------
  // Configurations
  public $timestamps = false;

  protected $fillable = array(
    'process_log_id',
    'ng_id',
    'ng_title',
    'quantity'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'process_log' => array(self::BELONGS_TO, 'ProcessLog'),
    'ng_detail' => array(self::BELONGS_TO, 'NgDetail')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array();

  // ---------------------------------------------
  // Ardent Hooks

}
