<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Wip extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'line_id',
    'product_id',
    'title'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships
/*
  public static $relationsData = array(
    'line' => array(self::BELONGS_TO, 'Line'),
    'product' => array(self::BELONGS_TO, 'Product'),
    'processes' => array(self::BELONGS_TO_MANY, 'Process', 'table' => 'wip_process')
  );
*/
public function line()
{
  return $this->belongsTo('Line');
}

public function product()
{
  return $this->belongsTo('Product');
}

public function processes()
{
    return $this->belongsToMany('Process', 'wip_process')->withPivot('sort');
}

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'line_id' => 'required|exists:lines,id',
    'product_id' => 'required|exists:products,id',
    'title' => 'required|max:300'
  );

  // ---------------------------------------------
  // Ardent Hooks

}
