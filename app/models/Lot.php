<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Lot extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'wip_id',
    'wip_title',
    'number',
    'quantity'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships
/*
  public static $relationsData = array(
    'wip' => array(self::BELONGS_TO, 'WIP')
  );
*/
public function wip()
{
  return $this->belongsTo('Wip');
}

public function processes()
{
    return $this->belongsToMany('Process')->withPivot('sort', 'process_log_id', 'qty');
}
  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'wip_id' => 'required|exists:wips,id'
  );

  // ---------------------------------------------
  // Ardent Hooks

}
