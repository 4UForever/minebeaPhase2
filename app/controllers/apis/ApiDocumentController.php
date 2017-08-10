<?php       

use Carbon\Carbon;                                                                               

class ApiDocumentController extends ApiBaseController {  
  
  // -------------------------------------------------------------
  // Dependancies
  
  protected $document;         
  
  // -------------------------------------------------------------
  // Configurations         
  
  // -------------------------------------------------------------
  // Constructor
  
  public function __construct(Document $document) {
    $this->document = $document;                   
  }                                                   
                                
  // -------------------------------------------------------------
  // Testing
  
  public function test() {                               
    
  }        
                                
  // -------------------------------------------------------------
  // CRUD       
                                
  // -------------------------------------------------------------
  // File download
  
  public function download() {
    $document_name = Request::segment(3);
    
    $documents = $this->document
                      ->where('title', strtoupper($document_name))
                      ->take(1) 
                      ->get(); 
    
    if ($documents->isEmpty()) {
      $errors = array('A PI Document file cannot be found');
      throw new ApiException($errors, 404);
    }          
    
    return Response::download($documents->first()->file_path);
  }                                                   

}