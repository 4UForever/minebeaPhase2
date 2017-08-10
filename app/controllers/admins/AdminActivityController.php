<?php       

use Carbon\Carbon;                                     

class AdminActivityController extends AdminBaseController {                                                
                                
  // -------------------------------------------------------------
  // Dependencies
  
  protected $line;                                                   
                                
  // -------------------------------------------------------------
  // Constructor
  
  public function __construct(Activity $activity) {
    $this->activity = $activity;                               
  }                                                                     
                                
  // -------------------------------------------------------------
  // Configurations
  
  protected $limit = 20;   
  protected $comment_substr_length = 50;   
                                
  // -------------------------------------------------------------
  // Lab
  
  public function getLab() {    
    $ch = curl_init(); 
 
    $arr = array();
    array_push($arr, "X-Parse-Application-Id: l2eMirrLUGiRUHWxOrSNyzjBbNwRKSP5GHLgOyjH");
    array_push($arr, "X-Parse-REST-API-Key: pAv5s6D60XwoIKuB9KyxbYtfAZeO2vs4g5NMRKGT");
    array_push($arr, "Content-Type: application/json");
     
    curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);
    curl_setopt($ch, CURLOPT_URL, 'https://api.parse.com/1/push');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{ "channel": "en","data": { "alert": "Foo" } }'); 
     
    $result = curl_exec($ch);
    curl_close($ch);
  }
                                
  // -------------------------------------------------------------
  // CRUD
  
  public function getIndex() {                   
    $headers = array('ID', 'Type', 'User', 'Line', 'Product', 'Process', 'Comment', 'Created at');
    $id = 'activities';
    $url = url('admin/activity/data-table');
    $datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers'))->render();
    
    return View::make('admins.activities.index', compact('datatable'));
  }         
                                
  // -------------------------------------------------------------
  // Data table
  
  public function getDataTable() {    
    $offset = Input::get('start');
    $limit = Input::get('length'); 
                                                   
    $query = $this->activity->skip($offset)->take($limit); 
                  
    $cols = array(
      'id',
      'type_title',     
      'user_full_name',     
      'line_title',     
      'product_title',     
      'process_title',     
      'comment',     
      'created_at', 
    );
             
    $orders = Input::get('order');
         
    foreach ($orders as $order) { 
      $col_index = $order['column'];
      $query->orderBy($cols[$col_index], $order['dir']);
    }
      
    foreach (Input::get('columns') as $column) {  
      $search = trim($column['search']['value']);           
      if (! empty($search)) {
        $query->where($column['data'], 'LIKE', "%{$column['search']['value']}%");
      }      
    }  
                                 
    $count_query = clone $query;                                
    
    $data = $query->get();  
    
    foreach ($data as &$item) {   
      $comment = $item->comment;
      $comment_length = strlen($comment);
      
      if ($comment_length > $this->comment_substr_length) {
        $sub = substr($comment, 0, $this->comment_substr_length);
        $full = $item->comment;
        
        $item->comment = View::make('admins.misc.read_more', compact('sub', 'full'))->render();
      }       
      
      $type_color = $item->type_id == $item::START_TYPE ? 'success' : 'warning';
      $item->type_title = "<span class='text-$type_color'>{$item->type_title}</span>"; 
    }                
    
    $response = array(
      'draw' => (int)Input::get('draw'),
      'recordsTotal' => $this->activity->count(),
      'recordsFiltered' => $count_query->skip(0)->count(),
      'data' => $data,
    );
    
    return Response::json($response);
  }                    

}