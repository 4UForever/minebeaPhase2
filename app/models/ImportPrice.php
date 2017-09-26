<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class ImportPrice extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'year',
    'month',
    'line_id',
    // 'line_title',
    'product_id',
    // 'product_title',
    'process_id',
    // 'process_number',
    // 'process_title',
    'circle_time',
    'unit_price'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'line' => array(self::BELONGS_TO, 'Line'),
    'product' => array(self::BELONGS_TO, 'Product'),
    'process' => array(self::BELONGS_TO, 'Process')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'line_id' => 'required|exists:lines,id',
    'product_id' => 'required|exists:products,id',
    'process_id' => 'required|exists:processes,id'
  );

  // ---------------------------------------------
  // Ardent Hooks

}
