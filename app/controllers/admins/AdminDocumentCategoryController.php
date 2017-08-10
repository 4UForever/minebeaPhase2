<?php

use Carbon\Carbon;

class AdminDocumentCategoryController extends AdminBaseController {

  // -------------------------------------------------------------
  // Dependencies

  protected $document_category;

  // -------------------------------------------------------------
  // Constructor

  public function __construct(DocumentCategory $document_category) {
    $this->document_category = $document_category;
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
    $headers = array('ID', 'Title', 'Created at', 'Updated at', '');
    $id = 'document_categories';
    $url = url('admin/document-category/data-table');
    $datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers'))->render();
    return View::make('admins.document_categories.index', compact('datatable'));
  }

  public function getCreate() {
    return View::make('admins.document_categories.create');
  }

  public function postCreate() {
    $this->document_category->title = Input::get('title');

    if (! $this->document_category->save()) {
      $errors = $this->document_category->errors()->all();
      return Redirect::to('admin/document-category/create')->withErrors($errors)->withInput();
    }

    return Redirect::to("admin/document-category")->with('success', "A document category <i>{$this->document_category->title}</i> is successfully created");
  }

  public function getUpdate($id) {
    $document_category = $this->document_category->find($id);

    if (empty($document_category->id)) {
      return Redirect::to('admin/document-category')->withErrors(array("A document category id $id could not be found"));
    }

    return View::make('admins.document_categories.update', compact('document_category'));
  }

  public function postUpdate($id) {
    $this->document_category = $this->document_category->find($id);

    if (empty($this->document_category->id)) {
      return Redirect::to('admin/document-category')->withErrors(array("A document category id $id could not be found"));
    }

    $this->document_category->title = Input::get('title');

    if (! $this->document_category->save()) {
      $errors = $this->document_category->errors()->all();
      return Redirect::to("admin/document-category/$id/update")->withErrors($errors)->withInput();
    }

    return Redirect::to("admin/document-category")->with('success', "A document category <i>{$this->document_category->title}</i> is successfully updated");
  }

  public function getDelete($id) {
    $error = array();

    $document_category = $this->document_category->find($id);

    if (empty($document_category->id)) {
      $errors = array("A document category id $id cannot be found");
      return Redirect::to('admin/document-category')->withErrors($errors);
    }

    return View::make('admins.document_categories.delete', compact('document_category'));
  }

  public function postDelete($id) {
    $error = array();

    $document_category = $this->document_category->find($id);

    if (empty($document_category->id)) {
      $errors = array("A document category id $id cannot be found");
      return Redirect::to('admin/document-category')->withErrors($errors);
    }

    if (! $document_category->delete()) {
      $errors = $document_category->errors()->all();
      return Redirect::to('admin/document-category')->withErrors($errors);
    }

    return Redirect::to("admin/document-category")->with('success', "A document category <i>{$document_category->title}</i> is successfully deleted");
  }

  // -------------------------------------------------------------
  // File download

  public function getDownload($id) {

    $document = $this->document->find($id);

    if (empty($document->id)) {
      return Redirect::to('admin/document')->withErrors(array("A document id {$document->id} could not be found"));
    }

    $headers = array(
      'Content-Type: application/pdf',
    );
    return Response::download($document->file_path, $headers);
  }

  // -------------------------------------------------------------
  // Ajax

  public function getProductProcesses() {
    $id = Input::get('id');
    $line_id = Input::get('line_id');

    $line = $this->line
                 ->with('products', 'processes')
                 ->where('id', $line_id)
                 ->get()
                 ->first();

    $products = $line->products;
    $product_select = View::make('admins.documents.model_product_select', compact('products', 'id'))->render();

    $processes = $line->processes;
    $process_select = View::make('admins.documents.model_process_select', compact('processes', 'id'))->render();

    $response = array(
      'product_select' => $product_select,
      'process_select' => $process_select,
    );

    return Response::json($response);
  }

  public function getProductProcessesPair() {
    $lines = $this->line
                  ->with('products', 'processes')
                  ->get();

    if ($lines->isEmpty()) {
      return Response::json('There is no more available lines', 404);
    }

    $first_line = $lines->first();
    $first_line_product = $first_line->products->first();
    $first_line_process = $first_line->processes->first();

    $id = uniqid();

    $document_index = array(
      'id' => $id,
      'line_id' => $first_line->id,
      'product_id' => $first_line_product->id,
      'process_id' => $first_line_process->id,
    );

    $last_row = TRUE;

    return View::make('admins.documents.model_process_row', compact('last_row', 'document_index', 'lines', 'id'));
  }

  // -------------------------------------------------------------
  // Data table

  public function getDataTable() {
    $offset = Input::get('start');
    $limit = Input::get('length');

    $query = $this->document_category->skip($offset)->take($limit);

    $cols = array(
      'id',
      'title',
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

    $document_categories = $query->get();

    $items = array();
    foreach ($document_categories as $key => &$document_category) {
      $items[$key] = $document_category->toArray();

      $buttons = array(
        array(
          'url' => url("admin/document-category/{$document_category->id}/update"),
          'type' => 'warning',
          'text' => 'Edit',
        ),
        array(
          'url' => url("admin/document-category/{$document_category->id}/delete"),
          'type' => 'danger',
          'text' => 'Delete',
        ),
      );

      $items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
    }

    $response = array(
      'draw' => (int)Input::get('draw'),
      'recordsTotal' => $this->document_category->count(),
      'recordsFiltered' => $count_query->skip(0)->count(),
      'data' => $items,
    );

    return Response::json($response);
  }

}
