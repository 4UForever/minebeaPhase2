<?php       

use Carbon\Carbon;                                     

class AdminDocumentController extends AdminBaseController {                                                
                                
  // -------------------------------------------------------------
  // Dependencies
                                                                  
  protected $document;                                                 
  protected $document_index;                                                 
  protected $document_category;                                                 
  protected $process;                                                 
  protected $line;                                                 
  protected $product;                                                 
                                
  // -------------------------------------------------------------
  // Constructor
  
  public function __construct(
    Document $document, DocumentIndex $document_index, DocumentCategory $document_category,
    Process $process, Product $product, Line $line
  ) {   
    $this->document = $document;                              
    $this->document_index = $document_index;                           
    $this->document_category = $document_category;                           
    $this->product = $product;                              
    $this->process = $process;                              
    $this->line = $line;                              
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
    $headers = array('ID', 'Title', 'Category', 'Bindings', 'Created at', 'Updated at', '');
    $id = 'documents';
    $url = url('admin/document/data-table');
    $datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers'))->render();
    
    return View::make('admins.documents.index', compact('datatable')); 
  }   
  
  public function getCreate() {           
    $lines = $this->line->with('products', 'processes')->get();
    
    $first_line = $lines->first();
    $first_line_product = $first_line->products->first();
    $first_line_process = $first_line->processes->first();  
    
    $default_document_indices = array(
      array(
        'id' => uniqid(),
        'line_id' => $first_line->id,
        'product_id' => $first_line_product->id,
        'process_id' => $first_line_process->id,
      ),
    );
    
    $document_indices = Input::old('document_indices', $default_document_indices);   
    
    $document_indices = array_values($document_indices);
    
    $document_categories =  $this->document_category->get();
    
    return View::make('admins.documents.create', compact('document_indices', 'lines', 'document_categories'));
  }   
  
  public function postCreate() { 
    if (Input::hasFile('upload_documents')) {  
      $files = Input::file('upload_documents');
      $document_indices = Input::get('document_indices');
      $document_category_id = Input::get('document_category_id');
      
      foreach ($files as $file) {              
        $file_storage_path = $this->document->documentStoragePath();
        $ascii_file_name = base64_encode($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        
        $file->move($file_storage_path, $ascii_file_name); 
                                                                
        $document = new $this->document;
        $document->title = $file->getClientOriginalName();
        $document->document_category_id = $document_category_id;
        $document->file_path = $file_storage_path . '/' . $ascii_file_name;
        
        if (! $document->save()) {  
          $errors = $document->errors()->all(); 
          @unlink($file_storage_path . '/' . $file->getClientOriginalName());  
          return Redirect::to('admin/document/create')->withErrors($errors)->withInput();
        }                                                       
        
        foreach ($document_indices as $document_index) {   
          $new_document_index = new $this->document_index;
          $new_document_index->document_id = $document->id;
          $new_document_index->process_id = $document_index['process_id'];
          $new_document_index->product_id = $document_index['product_id'];
          $new_document_index->line_id = $document_index['line_id'];
          
          if (! $new_document_index->save()) {                          
            $document->delete();
            $errors = $new_document_index->errors()->all();  
            @unlink($file_storage_path . '/' . $ascii_file_name);  
            return Redirect::to('admin/document/create')->withErrors($errors)->withInput();
          }
        }
      }                                                                               
    }
    else {  
      $errors = array('At least one document is required');   
      return Redirect::to('admin/document/create')->withErrors($errors)->withInput();
    }                                             
    
    return Redirect::to("admin/document")->with('success', "A document <i>{$this->document->title}</i> is successfully created");
  }
  
  public function getUpdate($id) { 
    $document = $this->document->with('document_category', 'document_indices.process', 'document_indices.product', 'document_indices.line')->find($id);
    
    if (empty($document->id)) {
      return Redirect::to('admin/document')->withErrors(array("A document id $id could not be found"));
    }                                                            
    
    $document_indices = array();
    
    foreach ($document->document_indices as $document_index) { 
      $document_indices[] = $document_index;
    }
    
    $selected_line_ids = array_keys($document_indices);
    $document_indices = array_values($document_indices);
    $lines = $this->line->with('products', 'processes')->get(); 
    
    $document_categories =  $this->document_category->get();
    
    return View::make('admins.documents.update', compact('document', 'document_indices', 'document_categories', 'lines', 'selected_line_ids'));
  }
  
  public function postUpdate($id) { 
    $this->document = $this->document->with('document_indices.process', 'document_indices.product')->find($id);
    
    if (empty($this->document->id)) {
      return Redirect::to('admin/document')->withErrors(array("A document id $id could not be found"));
    }   
    
    if (Input::hasFile('upload_documents')) {  
      $file = Input::file('upload_documents');
      
      $file_storage_path = $this->document->documentStoragePath();
      
      try {
        $ascii_file_name = base64_encode($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
        $file->move($file_storage_path, $ascii_file_name);   
      }
      catch (Exception $e) {                             
        $errors = array($e->getMessage());  
        return Redirect::to("admin/document/$id/update")->withErrors($errors)->withInput();
      }     
                                                              
      $this->document->title = $file->getClientOriginalName();
      $this->document->file_path = $file_storage_path . '/' . $ascii_file_name;  
    }
    
    $this->document->document_category_id = Input::get('document_category_id');
    
    if (! $this->document->updateUniques()) {  
      $errors = $this->document->errors()->all();   
      return Redirect::to("admin/document/$id/update")->withErrors($errors)->withInput();
    } 
                                                        
    $this->document_index->where('document_id', $this->document->id)->delete();
                   
    $document_indices = Input::get('document_indices'); 
    
    foreach ($document_indices as $document_index) {   
      $new_document_index = new $this->document_index;
      $new_document_index->document_id = $this->document->id;
      $new_document_index->product_id = $document_index['product_id'];
      $new_document_index->process_id = $document_index['process_id'];
      $new_document_index->line_id = $document_index['line_id'];
      
      if (! $new_document_index->save()) {                               
        $errors = $new_document_index->errors()->all();   
        return Redirect::to("admin/document/$id/update")->withErrors($errors)->withInput();
      }
    }
    
    return Redirect::to("admin/document")->with('success', "A document <i>{$this->document->title}</i> is successfully updated");
  } 
  
  public function getDelete($id) {
    $error = array();    
    
    $document = $this->document->find($id);
    
    if (empty($document->id)) { 
      $errors = array("A document id $id cannot be found");
      return Redirect::to('admin/document')->withErrors($errors);
    }      
     
    return View::make('admins.documents.delete', compact('document'));
  }
  
  public function postDelete($id) {
    $error = array();    
    
    $document = $this->document->find($id);
    
    if (empty($document->id)) { 
      $errors = array("A document id $id cannot be found");
      return Redirect::to('admin/document')->withErrors($errors);
    }                          
    
    if (! $document->delete()) {
      $errors = $document->errors()->all();   
      return Redirect::to('admin/document')->withErrors($errors);
    }                        
    
    return Redirect::to("admin/document")->with('success', "A document <i>{$document->title}</i> is successfully deleted");                                                                                     
  } 
                                
  // -------------------------------------------------------------
  // File download
  
  public function getDownload($id) {
    $document = $this->document->find($id); 
    
    if (empty($document->id)) {
      return Redirect::to('admin/document')->withErrors(array("A document id {$document->id} could not be found"));
    }          
    
    return Response::download($document->file_path, $document->title);
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
                        
    $query = $this->document
                  ->with('document_category', 'document_indices.product', 'document_indices.process', 'document_indices.line')
                  ->skip($offset)
                  ->take($limit);
                  
    $cols = array(
      'id',                       
      'title',      
      'document_category',  
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
                   
    $documents = $query->get();
    
    $items = array();
    foreach ($documents as $key => &$document) {  
      $items[$key] = $document->toArray();        
      
      $items[$key]['document_category'] = $document->document_category->title;
      
      $items[$key]['relationships'] = array();
      
      $bindings = array();
      foreach ($document->document_indices as $document_index) {  
        $combinations = array(
          empty($document_index->line->title) ? '' : $document_index->line->title,
          empty($document_index->product->title) ? '' : $document_index->product->title,
          empty($document_index->process->number) ? '' : $document_index->process->number,
        );                                               
        
        $bindings[] = implode(' | ', $combinations);                   
      }
      
      $params = array(
        'items' => $bindings,
        'col_num' => 1,
      );         
      
      $items[$key]['bindings'][] = View::make('admins.misc.multi_item_cell', $params)->render();
       
      $buttons = array(
        array(
          'url' => url("admin/document/{$document->id}/download"),
          'type' => 'primary',
          'text' => 'Download',
        ),
        array(
          'url' => url("admin/document/{$document->id}/update"),
          'type' => 'warning',
          'text' => 'Edit',
        ),
        array(                               
          'url' => url("admin/document/{$document->id}/delete"),
          'type' => 'danger',
          'text' => 'Delete',
        ),
      );
      
      $items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render(); 
    }
    
    $response = array(
      'draw' => (int)Input::get('draw'),
      'recordsTotal' => $this->document->count(),  
      'recordsFiltered' => $count_query->skip(0)->count(),
      'data' => $items,
    );
    
    return Response::json($response);
  }               

}