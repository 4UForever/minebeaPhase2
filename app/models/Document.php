<?php
  
use LaravelBook\Ardent\Ardent;
use Carbon\Carbon;

class Document extends Ardent {
  
  // --------------------------------------------------------
  // Constants          
  
  // --------------------------------------------------------
  // Configurations
  
  protected $fillable = array(
    'title',
  );
  
  protected $guarded  = array();
  
  protected $hidden = array(
    'file_path',
    'document_category_id',
  );
  
  // --------------------------------------------------------
  // Relationships  
  
  public static $relationsData = array(        
    'document_indices' => array(self::HAS_MANY, 'DocumentIndex'),
    'document_category' => array(self::BELONGS_TO, 'DocumentCategory'),
  ); 

  // ---------------------------------------------
  // Validations  
  
  public static $rules = array(                                               
    'title' => 'unique:documents,title',                                       
    'document_category_id' => 'required|exists:document_categories,id',                                       
    'file_path' => 'required',                                       
  );
  
  // ---------------------------------------------
  // Ardent Hooks    
  
  public function afterDelete() {
    $file_path = $this->file_path;
    @unlink($file_path);
    
    foreach ($this->document_indices() as $document_index) {
      $document_index->delete();
    }
  }
  
  // ---------------------------------------------
  // Download URL
  
  public function getDownloadUrlAttribute() {
    return isset($this->attributes['download_url']) ? $this->attributes['download_url'] : NULL;
  }  
  
  public function setDownloadUrlAttribute($value = NULL) {
    $this->attributes['download_url'] = url("api/document/{$this->id}/download");
  }  
  
  // ---------------------------------------------
  // Formatter
  
  public function toArray() {
    $this->setDownloadUrlAttribute();
    return parent::toArray();
  }
  
  // ---------------------------------------------
  // Misc
  
  public function documentStoragePath() {
    return storage_path('documents');
  }                   
  
  
}
