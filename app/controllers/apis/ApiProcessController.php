<?php       

use Carbon\Carbon;                                                                               

class ApiProcessController extends ApiBaseController {  
  
  // -------------------------------------------------------------
  // Dependancies
  
  protected $activity; 
  protected $product; 
  protected $line; 
  protected $process; 
  
  // -------------------------------------------------------------
  // Constructor
  
  public function __construct(Activity $activity, Product $product, Line $line, Process $process) {
    $this->activity = $activity; 
    $this->product = $product; 
    $this->line = $line; 
    $this->process = $process; 
  }                                                   
                                
  // -------------------------------------------------------------
  // Testing
  
  public function test() {                               
    
  }        
                                
  // -------------------------------------------------------------
  // Actions
  
  public function checkStatus() {
    $process_id = Input::get('process_id');
    
    $this->process = $this->process->find($process_id);
    
    if (empty($this->process->id)) {
      $errors = array("Process id $process_id cannot be found");
      throw new ApiException($errors, 404);  
    }
    
    $line_id = Input::get('line_id');
    
    $this->line = $this->line->find($line_id);
    
    if (empty($this->line->id)) {
      $errors = array("Line id $line_id cannot be found");
      throw new ApiException($errors, 404);
    }
    
    $product_id = Input::get('product_id');
    
    $this->product = $this->product->find($product_id);
    
    if (empty($this->product->id)) {
      $errors = array("Model id $product_id cannot be found");
      throw new ApiException($errors, 404);
    }
    
    $user = Sentry::getUser();
    
    $this->activity->validateRelations($line_id, $product_id, $process_id, $user->id);
    
    $this->process->setStatusAttribute($line_id, $process_id);
    $this->process->setAvailableAttribute($user->id, $line_id, $process_id);
    
    $errors = $this->activity->errors()->all();
    
    if (! empty($errors)) {
      throw new ApiException($errors, 400);  
    }
    
    $message = "Successfully retrieve the status of process {$this->process->title}";
    
    return Response::api($message, 200, $this->process);
  }                                                                

}