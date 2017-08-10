<?php

use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Product extends Ardent {

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
    'lines' => array(self::BELONGS_TO_MANY, 'Line'),
    'activities' => array(self::HAS_MANY, 'Activity'),
    'document_indices' => array(self::HAS_MANY, 'DocumentIndex'),
    'parts' => array(self::HAS_MANY, 'Part'),
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
    foreach ($this->lines()->get() as $line) {
      $product_count = count($line->products);

      if ($product_count < 2 && ! $this->parent_is_deleted) {
        $this->errors()->add('line_id', "A model id {$this->id} cannot be delete because it is only one model of a line {$line->title}");
        return FALSE;
      }
    }

    return TRUE;
  }

  public function afterDelete() {
    $this->lines()->sync(array());

    foreach ($this->document_indices()->get() as $docuemnt_index) {
      $docuemnt_index->delete();
    }

    return TRUE;
  }

  // ---------------------------------------------
  // Misc

  public function makeNewProductsTable($new_products) {
    $id = 'new-products';
    $heads = array('Title', '');

    if (empty($new_products)) {
      $new_products = array();
    }

    $rows = array();

    foreach ($new_products as $key => $new_product) {
      $rows[] = $this->makeNewProductsTableRow($new_product);
    }

    return View::make('admins.misc.table', compact('id', 'heads', 'rows'))->render();
  }

  public function makeNewProductsTableRow($new_product) {
    $row = array();

    $uid = empty($new_product['id']) ? uniqid() : $new_product['id'];

    $params = array(
      'name' => "new_products[$uid][title]",
      'value' => $new_product['title'],
    );

    $row['title'] = $new_product['title'] . View::make('admins.misc.input_hidden', $params)->render();

    $buttons = array(
      array(
        'url' => url("admin/model/create-form") . '?' . http_build_query($new_product),
        'type' => 'warning',
        'text' => 'Edit',
      ),
      array(
        'url' => url("admin/model/delete-form"),
        'type' => 'danger',
        'text' => 'Delete',
      ),
    );

    $row['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();

    return View::make('admins.misc.table_row', compact('row'))->render();
  }


}
