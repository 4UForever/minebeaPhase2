<?php       

use Carbon\Carbon;                                     

class AdminPermissionController extends AdminBaseController {                                                
                                
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
  
  // ----------------------------------------------------------
  // Permissions             
  
  public function getIndex() {
    $groups = $this->sentry_group->get();          
    $permissions = $this->permission->get();
    
    return View::make('admins.permissions.index', compact('groups', 'permissions'));
  }                                  
  
  public function postIndex() {
    $group_permissions = Input::get('permissions');
    
    $all_permissions = $this->permission->get();
    
    foreach ($group_permissions as $group_id => $permissions) {
      $group = $this->sentry_group->find($group_id);
      
      if (empty($group->id)) {
        $errors = array("Group id $group_id cannot be found");
        return Redirect::to('admin/permission')->withErrors($errors);
      }
      
      $all_permissions = $this->permission->get();
      
      $group_permissions = array();
      
      foreach ($all_permissions as $permission) {
        $group_permissions[$permission->key] = empty($permissions[$permission->key]) ? 0 : 1;
      }  
                  
      $group->permissions = $group_permissions;
      
      if (! $group->save()) {  
        return Redirect::to('admin/permission')->withErrors($group->errors());
      }
    }                                                                                                    
    
    return Redirect::to('admin/permission')->with('success', 'Permission configuration is successfully saved');
  }              

}