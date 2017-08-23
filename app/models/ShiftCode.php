<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class ShiftCode extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'label',
    'time_string'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array();

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'label' => 'required',
    'time_string' => 'required'
  );

  // ---------------------------------------------

}
