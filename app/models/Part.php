<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Part extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'product_id',
    'process_id',
    'number',
    'name'
  );

  protected $guarded  = array();

  protected $hidden = array('pivot');

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'product' => array(self::BELONGS_TO, 'Product'),
    'process' => array(self::BELONGS_TO, 'Process'),
    'iqc_lots' => array(self::HAS_MANY, 'IqcLot')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'product_id' => 'required|exists:products,id',
    'process_id' => 'required|exists:processes,id',
    'number' => 'required',
    'name' => 'required|max:128'
  );

  // ---------------------------------------------
  // Ardent Hooks
  public function showPart(){
    $part = Part::find(1);
    echo "{$part->name}, {$part->products->title} - {$part->processes->number}";
  }

}
