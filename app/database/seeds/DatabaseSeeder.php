<?php

class DatabaseSeeder extends Seeder {

  public function run() {
    Eloquent::unguard();
    $this->call('PermissionSeeder');  
    $this->call('SentryGroupSeeder');  
    $this->call('SentryUserSeeder');  
    $this->call('LineSeeder');  
    $this->call('ProductSeeder');  
    $this->call('ProcessSeeder');  
    $this->call('DocumentCategorySeeder');  
    $this->call('DocumentSeeder');  
    $this->call('DocumentIndexSeeder');  
    $this->call('ActivitySeeder');  
  }     

}     

class PermissionSeeder extends Seeder {
  
  public function run() {
    DB::table('permissions')->delete();
    
    $permissions = array(
      array(
        'title' => 'Manage user groups',
        'key' => 'manage_user_groups',
      ),
      array(
        'title' => 'Manage users',
        'key' => 'manage_users',
      ),
      array(
        'title' => 'Manage models',
        'key' => 'manage_models',
      ),
      array(
        'title' => 'Manage production lines',
        'key' => 'manage_production_lines',
      ),
      array(
        'title' => 'Manage processes',
        'key' => 'manage_processes',
      ),
      array(
        'title' => 'Manage documents',
        'key' => 'manage_documents',
      ),
      array(
        'title' => 'Manage activities',
        'key' => 'manage_activities',
      ),
      array(
        'title' => 'Work on model\'s processes',
        'key' => 'work_on_model_processes',
      ),
      array(
        'title' => 'View documents',
        'key' => 'view_documents',
      ),     
    );
    
    foreach ($permissions as $permission) { 
      Permission::create($permission);
    }                              
  }
  
}                                             

class SentryGroupSeeder extends Seeder {
  
  public function run() {
    DB::table('groups')->delete();     
    
    $permissions = Permission::get();
    
    $groups = array(
      array(
        'name' => 'Administrator',
        'permissions' => array(
          'manage_user_groups' => 1,
          'manage_users' => 1,      
          'manage_models' => 1,
          'manage_production_lines' => 1,
          'manage_processes' => 1,
          'manage_documents' => 1,
          'manage_activities' => 1,
        ),      
      ),
      array(
        'name' => 'Engineer',                  
        'permissions' => array(
          'work_on_model_processes' => 1,
          'view_documents' => 1,
        ),
      ),
    );
    
    foreach ($groups as $group) {
      Sentry::createGroup($group);  
    }                             
  }
  
}

class SentryUserSeeder extends Seeder {
  
  public function run() {
    DB::table('users')->delete();
    DB::table('users_groups')->delete();
    
    $users = array(                   
      array(
        'email' => 'admin-test@devsenses.net',
        'password' => '1234',
        'qr_code' => 'foo',
        'activated' => TRUE,     
        'first_name' => 'I am',
        'last_name' => 'admin',
        'group' => 'Administrator',
      ),                    
      array(
        'email' => 'engineer-test@devsenses.net',
        'password' => '1234',
        'qr_code' => 'bar',
        'activated' => TRUE,    
        'first_name' => 'I am',
        'last_name' => 'engineer',
        'group' => 'Engineer',
      ),                 
      array(
        'email' => 'engineer-test-2@devsenses.net',
        'password' => '1234',
        'qr_code' => 'baz',
        'activated' => TRUE,    
        'first_name' => 'I am',
        'last_name' => 'engineer 2',
        'group' => 'Engineer',
      ),           
    );                                                
    
    foreach ($users as $user) {  
      $group_name = $user['group'];
      unset($user['group']);
      
      $user = Sentry::createUser($user);   
      
      $adminGroup = Sentry::findGroupByName($group_name);
      $user->addGroup($adminGroup);
    }                              
  }
  
} 

class LineSeeder extends Seeder {
  
  public function run() {
    DB::table('lines')->delete();    
    
    $lines = array(                   
      array(
        'title' => 'Line 1',  
      ),                
      array(
        'title' => 'Line 2',  
      ),                
      array(
        'title' => 'Line 3',  
      ),           
    );                                                
    
    foreach ($lines as $line) {   
      Line::create($line);
    }                              
  }
  
} 

class ProductSeeder extends Seeder {
  
  public function run() {
    DB::table('products')->delete();    
    
    $products = array(                   
      array(
        'title' => 'Model 1',  
      ),                     
      array(
        'title' => 'Model 2',  
      ),                     
      array(
        'title' => 'Model 3',  
      ),           
    );                    
    
    $line_ids = array_fetch(Line::get()->toArray(), 'id');                       
    
    foreach ($products as $product) {     
      $product = Product::create($product);
      
      $product_line_ids = $line_ids;                                      
      $rand = rand(0, count($line_ids) - 1);
      unset($product_line_ids[$rand]); 
      
      $product->lines()->sync($product_line_ids);
    }                              
  }
  
} 

class ProcessSeeder extends Seeder {
  
  public function run() {
    DB::table('processes')->delete();                                                              
    
    $processes = array(                   
      array(
        'title' => 'Process 1',           
        'number' => '100',           
      ),                      
      array(
        'title' => 'Process 2',            
        'number' => '101',            
      ),                      
      array(
        'title' => 'Process 3',           
        'number' => '102',             
      ),                   
      array(
        'title' => 'Process 4',            
        'number' => '103',            
      ),                   
      array(
        'title' => 'Process 5',           
        'number' => '104',             
      ),           
    );                               
    
    $users = Sentry::findAllUsersWithAccess('work_on_model_processes'); 
    
    $user_ids = array();
    
    foreach ($users as $user) {
      $user_ids[] = $user->id;
    }
                                                   
    $line_ids = array_fetch(Line::get()->toArray(), 'id'); 
    $line_count = count($line_ids);    
    
    foreach ($processes as $process) { 
      $rand = rand(0, $line_count - 1); 
      $process['line_id'] = $line_ids[$rand];
      $process = Process::create($process);
      $process->users()->sync($user_ids);
    }                              
  }
  
}  

class DocumentCategorySeeder extends Seeder {
  
  public function run() {
    DB::table('document_categories')->delete();  
    
    $document_categories = array(                   
      array(
        'title' => 'PI',                                
      ),           
      array(
        'title' => 'RE',                                
      ),           
      array(
        'title' => 'PI-PR',                                
      ),           
      array(
        'title' => 'PI-SET',                                
      ),           
    );                                                   
    
    foreach ($document_categories as $document_category) {     
      DocumentCategory::create($document_category);  
    }                              
  }
  
}

class DocumentSeeder extends Seeder {
  
  public function run() {
    DB::table('documents')->delete();  
    
    $document_categories = DocumentCategory::get();
    
    $counter = 0;
    
    $documents = array(                   
      array(
        'title' => 'PI 1',                 
        'document_category_id' => $document_categories[$counter]->id,                 
        'file_path' => storage_path('documents/pi.pdf'),
      ),                     
      array(
        'title' => 'PI 2',                 
        'document_category_id' => $document_categories[$counter]->id,                 
        'file_path' => storage_path('documents/pi.pdf'),
      ),           
      array(
        'title' => 'RE 1',                  
        'document_category_id' => $document_categories[++$counter]->id,
        'file_path' => storage_path('documents/re.pdf'),
      ),           
      array(
        'title' => 'RE 2',                  
        'document_category_id' => $document_categories[$counter]->id,
        'file_path' => storage_path('documents/re.pdf'),
      ),           
      array(
        'title' => 'PI-PR 1',                  
        'document_category_id' => $document_categories[++$counter]->id,
        'file_path' => storage_path('documents/pi_pr.pdf'),
      ),         
      array(
        'title' => 'PI-PR 2',                  
        'document_category_id' => $document_categories[$counter]->id,
        'file_path' => storage_path('documents/pi_pr.pdf'),
      ),           
      array(
        'title' => 'PI-SET 1',                  
        'document_category_id' => $document_categories[++$counter]->id,
        'file_path' => storage_path('documents/pi_set.pdf'),
      ),        
      array(
        'title' => 'PI-SET 2',                  
        'document_category_id' => $document_categories[$counter]->id,
        'file_path' => storage_path('documents/pi_set.pdf'),
      ),           
    );                                                   
    
    foreach ($documents as $document) {     
      $document = Document::create($document);  
    }                              
  }
  
}

class DocumentIndexSeeder extends Seeder {
  
  public function run() {
    DB::table('document_indices')->delete();  
    
    $documents = Document::get();
    $lines = Line::with('products', 'processes')->get()->toArray();
    
    $document_indice = array();                                                   
    
    foreach ($documents as $document) {   
      do {      
        $line_random = rand(0, count($lines) - 1);               
      } while (
        empty($lines[$line_random]) ||
        empty($lines[$line_random]['products']) ||
        empty($lines[$line_random]['processes'])
      );
      
      $line = $lines[$line_random];  
      
      do {                                                                 
        $product_random = rand(0, count($line['products']) - 1);
        $process_random = rand(0, count($line['processes']) - 1);
      } while (                           
        empty($line['products'][$product_random]) || 
        empty($line['processes'][$process_random])
      ); 
                                                           
      $product = $line['products'][$product_random];
      $process = $line['processes'][$process_random];
      
      $document_indice[] = array(
        'document_id' => $document['id'],
        'line_id' => $line['id'],
        'product_id' => $product['id'],
        'process_id' => $process['id'],
      );
    }
    
    foreach ($document_indice as $document_index) {     
      DocumentIndex::create($document_index);  
    }                              
  }
  
}  

class ActivitySeeder extends Seeder {
  
  public function run() {
    DB::table('activities')->delete();  
    
    $lines = Line::with('products', 'processes.users')->get(); 
    
    foreach ($lines as $key => $line) {
      if ($line->products->isEmpty()) {
        continue;
      }
      foreach ($line->products as $product) {
        if ($line->processes->isEmpty()) {
          continue;
        }
        foreach ($line->processes as $process) {
          if ($process->users->isEmpty()) {
            continue;
          }
          for ($i = 0; $i < 2; ++$i) {
            $user = $process->users->first();
            Sentry::login($user);
            
            $attr = array(  
              'type_id' =>  $i == 0 ? Activity::START_TYPE : Activity::END_TYPE,                 
              'user_id' => $user->id,          
              'line_id' => $line->id,       
              'process_id' => $process->id,      
              'product_id' => $product->id,       
              'comment' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum."
            ); 
            
            Activity::create($attr);
          }   
        }
      } 
    }
    
    
  }
  
}       
