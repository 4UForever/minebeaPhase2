<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class ProcessLog extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'user_id',
    'full_name',
    'user_email',
    'line_id',
    'line_title',
    'product_id',
    'product_title',
    'process_id',
    'process_number',
    'process_title',
    'shift_id',
    'shift_label',
    'shift_time',
    'dt',
    'setup',
    'wip_id',
    'wip_sort',
    'lot_id',
    'lot_number',
    'line_leader',
    'line_leader_name',
    'first_serial_no',
    'last_serial_no'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'process_log_parts' => array(self::HAS_MANY, 'ProcessLogPart'),
    'process_log_inputs' => array(self::HAS_MANY, 'ProcessLogInput'),
    'process_log_breaks' => array(self::HAS_MANY, 'ProcessLogBreak'),
    'process_log_ngs' => array(self::HAS_MANY, 'ProcessLogNg')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array();

  // ---------------------------------------------
  // Ardent Hooks

}
