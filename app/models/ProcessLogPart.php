<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class ProcessLogPart extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'process_log_id',
    'part_id',
    'part_number',
    'part_name'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'process_log' => array(self::BELONGS_TO, 'ProcessLog'),
    'part' => array(self::BELONGS_TO, 'Part')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array();

  // ---------------------------------------------
  // Ardent Hooks

}
