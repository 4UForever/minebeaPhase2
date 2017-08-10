<?php
  
use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class DocumentIndex extends Ardent {
  
  // --------------------------------------------------------
  // Configurations
  
  protected $fillable = array(
    'document_id', 
    'process_id', 
    'product_id',
    'line_id',
  );
  
  protected $guarded  = array();
  
  // --------------------------------------------------------
  // Relationships 
  
  public static $relationsData = array(        
    'document' => array(self::BELONGS_TO, 'Document'),
    'process' => array(self::BELONGS_TO, 'Process'),
    'product' => array(self::BELONGS_TO, 'Product'),
    'line' => array(self::BELONGS_TO, 'Line'),
  );

  // ---------------------------------------------
  // Validations  
  
  public static $rules = array(                                              
    'document_id' => 'required|exists:documents,id|unique_with:document_indices,product_id,process_id,line_id',
    'process_id' => 'required|exists:processes,id',                                           
    'product_id' => 'required|exists:products,id',                                           
    'line_id' => 'required|exists:lines,id',                                          
  );
  
  // ---------------------------------------------
  // Ardent Hooks  
  
}
