<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class ProcessLogBreak extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'process_log_id',
    'break_id',
    'break_code',
    'break_reason',
    'start_break',
    'end_break',
    'total_minute'
  );

  protected $guarded  = array();

  protected $hidden = array();

  public $timestamps = false;

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'process_log' => array(self::BELONGS_TO, 'ProcessLog'),
    'break_reason' => array(self::BELONGS_TO, 'BreakReason')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array();

  // ---------------------------------------------
  // Ardent Hooks

}
