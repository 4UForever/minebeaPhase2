<?php namespace ExcelBKK;
  
class ApiException extends \Exception {
  
  protected $messages;
  
  public function __construct($messages, $code = 0, Exception $previous = NULL) {
    $parent_message = implode(', ', $messages);
    parent::__construct($parent_message, $code, $previous);
    $this->messages = $messages;
  }
  
  public function getMessages() {
    return $this->messages;
  }
  
}