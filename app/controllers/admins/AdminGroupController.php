<?php       

use Carbon\Carbon;                                     

class AdminGroupController extends AdminBaseController {                                                
                                
  // -------------------------------------------------------------
  // Dependencies
  
  protected $sentry_group;                                             
  protected $permission;                                                  
                                
  // -------------------------------------------------------------
  // Constructor
  
  public function __construct(Permission $permission) {
    $this->permission = $permission;
    $this->sentry_group = Sentry::getGroupProvider()->createModel();
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
    $headers = array('ID', 'Title', 'Created at', 'Updated at', '');
    $id = 'groups';
    $url = url('admin/group/data-table');
    $datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers'))->render();
    
    return View::make('admins.groups.index', compact('datatable')); 
  }   
  
  public function getCreate() {  
    $permissions = $this->permission->get();  
    $group_permissions = Input::old('permission_ids', array());
    return View::make('admins.groups.create', compact('permissions', 'group_permissions'));
  }
  
  public function postCreate() {
    try {
      $data = array(
        'name' => Input::get('name'),
        'permissions' => array(),
      );
      
      $input_perms = Input::get('permission_ids', array());
      
      foreach ($input_perms as $key => $input_perm) {
        $data['permissions'][$key] = 1;
      }
      
      $group = Sentry::createGroup($data);
    }
    catch (Cartalyst\Sentry\Groups\NameRequiredException $e) {
      return Redirect::to('admin/group/create')->withErrors(array('Name field is required.'))->withInput();
    }
    catch (Cartalyst\Sentry\Groups\GroupExistsException $e) {
      return Redirect::to('admin/group/create')->withErrors(array('Group already exists.'))->withInput();
    } 
       
    return Redirect::to('admin/group')->with('success', "A user group <i>{$group->name}</i> is successfully created");
  }    
  
  public function getUpdate($id) { 
    $group = Sentry::findGroupById($id);
    
    if (empty($group->id)) {
      return Redirect::to('admin/group')->withErrors(array("A group id {$group->id} could not be found."));
    }
    
    $permissions = $this->permission->get();
    
    $group_permissions = $group->getPermissions();     
    
    return View::make('admins.groups.update', compact('group', 'permissions', 'group_permissions'));
  }
  
  public function postUpdate($id) {
    $group = Sentry::findGroupById($id);
    
    if (empty($group->id)) {
      return Redirect::to('groups')->withErrors(array("A group id {$group->id} could not be found."));
    }                                
    
    $group->name = Input::get('name', '');    
    
    $permissions = Input::get('permission_ids', array());
    
    $all_permissions = $this->permission->get();
    
    $group_permissions = array();
    
    foreach ($all_permissions as $permission) {
      $group_permissions[$permission->key] = empty($permissions[$permission->key]) ? 0 : 1;
    }  
                
    $group->permissions = $group_permissions;
    
    $errors = array();
    
    try {
      $group->save();
    }
    catch (Cartalyst\Sentry\Groups\NameRequiredException $e) {
        $errors[] = 'Name field is required';
    }
    catch (Cartalyst\Sentry\Groups\GroupExistsException $e) {
        $errors[] = 'Group already exists.';
    }
    catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
        $errors[] = 'Group was not found.';
    }
    
    if (! empty($errors)) {  
      return Redirect::to("admin/group/$id/update")->withErrors($errors);
    }                                                                                                    
    
    return Redirect::to("admin/group")->with('success', "A user group <i>{$group->name}</i> is successfully updated");
  }
  
  public function getDelete($id) {
    $error = array();
    
    try {                                 
      $group = Sentry::findGroupById($id);
      return View::make('admins.groups.delete', compact('group')); 
    }
    catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
      $error[] = "A group id $id cannot be found.";
      return Redirect::to('admin/group')->withErrors($error);
    }                                                
  }
  
  public function postDelete($id) {
    try {                                 
      $group = Sentry::findGroupById($id);
      $group->delete();
      return Redirect::to('admin/group')->with('success', "A group id <i>{$group->name}</i> is successfully deleted.");
    }
    catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
      return Redirect::to('admin/group')->withErrors(array("A group id <i>$id</i> cannot be found."));
    }                                                                                      
  }     
                                
  // -------------------------------------------------------------
  // Data table
  
  public function getDataTable() {    
    $offset = Input::get('start');
    $limit = Input::get('length'); 
                        
    $query = $this->sentry_group                 
                  ->skip($offset)
                  ->take($limit);
                  
    $cols = array(
      'id',
      'name',       
      'created_at',
      'updated_at',
    );
             
    $orders = Input::get('order');
         
    foreach ($orders as $order) { 
      $col_index = $order['column'];
      $query->orderBy($cols[$col_index], $order['dir']);
    }
         
    $count_query = clone $query;
                                   
    $search = Input::get('search');
    
    if (! empty($search['value'])) {
      $query->where('name', 'LIKE', "%{$search['value']}%");
    }
                        
    $groups = $query->get();
    
    $items = array();
    foreach ($groups as $key => &$group) {  
      $items[$key] = $group->toArray();                                                           
       
      $buttons = array(   
        array(
          'url' => url("admin/group/{$group->id}/update"),
          'type' => 'warning',
          'text' => 'Edit',
        ),
        array(                               
          'url' => url("admin/group/{$group->id}/delete"),
          'type' => 'danger',
          'text' => 'Delete',
        ),
      );
      
      $items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render(); 
    }
    
    $response = array(
      'draw' => (int)Input::get('draw'),
      'recordsTotal' => $this->sentry_group->count(), 
      'recordsFiltered' => $count_query->skip(0)->count(),
      'data' => $items,
    );
    
    return Response::json($response);
  }                    

}