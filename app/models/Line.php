<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Line extends Ardent {

  // --------------------------------------------------------
  // Configurations

  protected $fillable = array(
    'title',
  );

  protected $guarded  = array();

  protected $hidden = array('pivot');

  // --------------------------------------------------------
  // Relationships

  public static $relationsData = array(
    'products' => array(self::BELONGS_TO_MANY, 'Product'),
    'processes' => array(self::HAS_MANY, 'Process'),
    'activities' => array(self::HAS_MANY, 'Activity'),
    'document_indices' => array(self::HAS_MANY, 'DocumentIndex'),
    'wips' => array(self::HAS_MANY, 'Wip')
  );

  // ---------------------------------------------
  // Validations

  public static $rules = array(
    'title' => 'required|max:128',
  );

  // ---------------------------------------------
  // Ardent Hooks

  public function beforeDelete() {
    $products = $this->products()->get();

    foreach ($products as $product) {
      if ($product->lines()->count() == 1) {
        $product->parent_is_deleted = TRUE;
        $product->delete();
      }
    }

    $this->products()->sync(array());

    foreach ($this->processes()->get() as $process) {
      $process->parent_is_deleted = TRUE;
      $process->delete();
    }
  }


}
