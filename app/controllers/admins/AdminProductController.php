<?php

use Carbon\Carbon;

class AdminProductController extends AdminBaseController {

  // -------------------------------------------------------------
  // Dependencies

  protected $product;
  protected $line;
  protected $process;

  // -------------------------------------------------------------
  // Constructor

  public function __construct(Product $product, Line $line, Process $process) {
    $this->product = $product;
    $this->line = $line;
    $this->process = $process;
  }

  // -------------------------------------------------------------
  // Configurations

  protected $limit = 20;

  // -------------------------------------------------------------
  // Lab

  public function getLab() {
    //Artisan::call('db:seed');
  }

  // -------------------------------------------------------------
  // CRUD

  public function getIndex() {
    $headers = array('ID', 'Title', 'Lines', 'Created at', 'Updated at', '');
    $id = 'products';
    $url = url('admin/model/data-table');
    $datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers'))->render();

    return View::make('admins.products.index', compact('datatable'));
  }

  public function getCreate() {
    $lines = $this->line->get();
    return View::make('admins.products.create', compact('lines'));
  }

  public function postCreate() {
    $product = new $this->product;

    $product->title = Input::get('title');
    $line_ids = Input::get('line_ids', array());

    if (empty($line_ids)) {
      $errors = array('Please select at least one line');
      return Redirect::to('admin/model/create')->withErrors($errors)->withInput();
    }

    if (! $product->save()) {
      $errors = $product->errors()->all();
      return Redirect::to('admin/model/create')->withErrors($errors)->withInput();
    }

    if (! $product->lines()->sync($line_ids)) {
      $product->delete();
      $errors = array("Cannot attach this model's lines");
      return Redirect::to('admin/model/create')->withErrors($errors);
    }

    return Redirect::to("admin/model")->with('success', "A model <i>{$product->title}</i> is successfully created");
  }

  public function getUpdate($id) {
    $product = $this->product->with('lines')->find($id);

    if (empty($product->id)) {
      return Redirect::to('admin/model')->withErrors(array("A model id {$product->id} could not be found"));
    }

    $lines = $this->line->get();

    $product_line_ids = array();

    if (! $product->lines->isEmpty()) {
      $product_line_ids = array_fetch($product->lines->toArray(), 'id');
    }

    $view_params = compact('product', 'lines', 'product_line_ids');

    return View::make('admins.products.update', $view_params);
  }

  public function postUpdate($id) {
    $product = $this->product->with('lines')->find($id);

    if (empty($product->id)) {
      return Redirect::to('admin/model')->withErrors(array("A model id {$product->id} could not be found"));
    }

    $line_ids = Input::get('line_ids', array());

    if (empty($line_ids)) {
      $errors = array('Please select at least one line');
      return Redirect::to('admin/model/create')->withErrors($errors)->withInput();
    }

    if (! $product->lines()->sync($line_ids)) {
      $errors = array("Cannot attach this model's lines");
      return Redirect::to('admin/model')->withErrors($errors);
    }

    $product->title = Input::get('title');

    if (! $product->save()) {
      $errors = $product->errors()->all();
      return Redirect::to('admin/model')->withErrors($errors);
    }

    return Redirect::to("admin/model")->with('success', "A model <i>{$product->title}</i> is successfully updated");
  }

  public function getDelete($id) {
    $error = array();

    $product = $this->product->find($id);

    if (empty($product->id)) {
      $errors = array("A model id $id cannot be found");
      return Redirect::to('admin/model')->withErrors($errors);
    }

    return View::make('admins.products.delete', compact('product'));
  }

  public function postDelete($id) {
    $error = array();

    $product = $this->product->find($id);

    if (empty($product->id)) {
      $errors = array("A model id $id cannot be found");
      return Redirect::to('admin/model')->withErrors($errors);
    }

    if (! $product->delete()) {
      $errors = $product->errors()->all();
      return Redirect::to('admin/model')->withErrors($errors);
    }

    return Redirect::to("admin/model")->with('success', "A model <i>{$product->title}</i> is successfully deleted");
  }

  // -------------------------------------------------------------
  // Data table

  public function getDataTable() {
    $offset = Input::get('start');
    $limit = Input::get('length');

    $query = $this->product
                  ->with('lines')
                  ->skip($offset)
                  ->take($limit);

    $cols = array(
      'id',
      'title',
      'lines',
      'created_at',
      'updated_at',
    );

    $orders = Input::get('order');

    foreach ($orders as $order) {
      $col_index = $order['column'];
      $query->orderBy($cols[$col_index], $order['dir']);
    }

    $search = Input::get('search');

    if (! empty($search['value'])) {
      $query->where('title', 'LIKE', "%{$search['value']}%");
    }

    $count_query = clone $query;

    $products = $query->get();

    $items = array();
    foreach ($products as $key => &$product) {
      $items[$key] = $product->toArray();

      $lines_array = $product->lines->toArray();
      $line_ids = array_fetch($lines_array, 'title');

      $view_params = array(
        'items' => $line_ids,
        'col_num' => 3,
      );

      $items[$key]['lines'] = View::make('admins.misc.multi_item_cell', $view_params)->render();

      $buttons = array(
        array(
          'url' => url("admin/model/{$product->id}/update"),
          'type' => 'warning',
          'text' => 'Edit',
        ),
        array(
          'url' => url("admin/model/{$product->id}/delete"),
          'type' => 'danger',
          'text' => 'Delete',
        ),
      );

      $items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
    }

    $response = array(
      'draw' => (int)Input::get('draw'),
      'recordsTotal' => $this->product->count(),
      'recordsFiltered' => $count_query->skip(0)->count(),
      'data' => $items,
    );

    return Response::json($response);
  }

  // -------------------------------------------------------------
  // Form

  public function getCreateForm() {
    $title = Input::get('title', '');
    return View::make('admins.products.create_form', compact('title'));
  }

  public function postCreateFormValidate() {
    $input = Input::all();

    $rules = array(
      'title' => 'required|max:128',
    );

    $validator = Validator::make($input, $rules);

    if ($validator->fails()) {
      $messages = $validator->messages()->all();
      $response = View::make('admins.misc.errors', array('errors' => $messages))->render();
      return Response::json($response, 400);
    }

    $response = $this->product->makeNewProductsTableRow($input);

    return Response::json($response);
  }

}