<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class NgDetail extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'process_id',
    'title'
  );

  protected $guarded  = array();

  protected $hidden = array('pivot');

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'process' => array(self::BELONGS_TO, 'Process')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'process_id' => 'required|exists:processes,id',
    'title' => 'required|max:255'
  );

  // ---------------------------------------------

}
