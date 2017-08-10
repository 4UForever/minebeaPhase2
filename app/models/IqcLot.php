<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class IqcLot extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'part_id',
    'number',
    'supplier_name',
    'invoice_number',
    'quantity'
  );

  protected $guarded  = array();

  protected $hidden = array();

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'part' => array(self::BELONGS_TO, 'Part')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'part_id' => 'required|exists:parts,id',
    'number' => 'required',
    'quantity' => 'integer'
  );

  // ---------------------------------------------
  // Ardent Hooks

}
