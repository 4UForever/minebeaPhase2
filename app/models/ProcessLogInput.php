<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class ProcessLogInput extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'process_log_id',
    'part_id',
    'lot_type',
    'lot_number',
    'use_qty'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'process_log' => array(self::BELONGS_TO, 'ProcessLog')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array();

  // ---------------------------------------------
  // Ardent Hooks

}
