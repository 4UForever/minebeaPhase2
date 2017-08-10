<?php       

use Carbon\Carbon;                                                                               

class ApiDocumentCategoryController extends ApiBaseController {  
  
  // -------------------------------------------------------------
  // Dependancies
  
  protected $document; 
  protected $document_index; 
  protected $document_category; 
  
  // -------------------------------------------------------------
  // Configurations          
  
  // -------------------------------------------------------------
  // Constructor
  
  public function __construct(Document $document, DocumentIndex $document_index, DocumentCategory $document_category) {
    $this->document = $document; 
    $this->document_index = $document_index; 
    $this->document_category = $document_category; 
  }                                                   
                                
  // -------------------------------------------------------------
  // Testing
  
  public function test() {                               
    
  }        
                                
  // -------------------------------------------------------------
  // CRUD
  
  public function index() {
    $offset = Input::get('offset', 0);
    $limit = Input::get('limit', 25);
    
    $line_id = Input::get('line_id');
    $process_id = Input::get('process_id');
    $product_id = Input::get('product_id');
    
    $document_indices = $this->document_index
                             ->where('line_id', $line_id)
                             ->where('process_id', $process_id)
                             ->where('product_id', $product_id)
                             ->get();
    
    $document_ids = array();
    
    if (! $document_indices->isEmpty()) {          
      $document_ids = array_fetch($document_indices->toArray(), 'document_id');
    }                                                                         
    
    $eagor_load = array(
      'documents' => function($query) use($document_ids) {
        $query->whereIn('id', $document_ids);
      },
    );
    
    $document_categories = $this->document_category->with($eagor_load)->get(); 
            
    if ($document_categories->isEmpty()) {
      $messages = array("There is no document category");
      throw new ApiException($messages, 404);
    }
    
    $message = "Successfully retrieve a list of documents for line id $line_id process id $process_id, model id $product_id";
    
    return Response::api($message, 200, $document_categories, TRUE);
  }                                                              

}