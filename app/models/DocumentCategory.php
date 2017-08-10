<?php
  
use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class DocumentCategory extends Ardent {
  
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
    'documents' => array(self::HAS_MANY, 'Document'),
  );

  // ---------------------------------------------
  // Validations  
  
  public static $rules = array(                                               
    'title' => 'required|max:128',                                           
  );
  
  // ---------------------------------------------
  // Ardent Hooks  
  
  public function afterDelete() { 
    foreach ($this->documents()->get() as $document) {
      $document->delete();
    }         
  }               
  
  
}
