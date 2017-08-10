<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class BreakReason extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'code',
    'reason'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array();

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'code' => 'required|max:10',
    'reason' => 'required|max:255'
  );

  // ---------------------------------------------

}
