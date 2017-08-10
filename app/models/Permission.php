<?php
  
use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Permission extends Ardent {
  
  // --------------------------------------------------------
  // Configurations
  
  protected $fillable = array(
    'title',  
    'key',  
  );
  
  protected $guarded  = array();
  
  // --------------------------------------------------------
  // Relationships 

  // ---------------------------------------------
  // Validations  
  
  public static $rules = array(                                               
    'title' => 'required|max:128',                                            
    'key' => 'required|max:128',                                            
  );
  
  // ---------------------------------------------
  // Ardent Hooks                  
  
  
}
