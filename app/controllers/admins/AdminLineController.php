<?php

use Carbon\Carbon;

class AdminLineController extends AdminBaseController {

  // -------------------------------------------------------------
  // Dependencies

  protected $line;
  protected $product;
  protected $process;
  protected $user;

  // -------------------------------------------------------------
  // Constructor

  public function __construct(Line $line, Product $product, Process $process) {
    $this->line = $line;
    $this->product = $product;
    $this->process = $process;
    $this->user = Sentry::getUserProvider()->createModel();
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
    $headers = array('ID', 'Title', 'Models', 'Processes (Number)', 'Created at', 'Updated at', '');
    $id = 'lines';
    $url = url('admin/line/data-table');
    $datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers'))->render();

    return View::make('admins.lines.index', compact('datatable'));
  }

  public function getCreate() {
    $products = $this->product->get();

    $new_products = Input::old('new_products');
    $new_products_table = $this->product->makeNewProductsTable($new_products);

    $new_processes = Input::old('new_processes');
    $new_processes_table = $this->process->makeNewProcessesTable($new_processes);

    return View::make('admins.lines.create', compact('products', 'new_processes_table', 'new_products_table'));
  }

  public function postCreate() {
    $product_ids = Input::get('product_ids', array());
    $new_products = Input::get('new_products', array());

    if (empty($product_ids) && empty($new_products)) {
      $errors = array('Please select or create at least one model');
      return Redirect::to('admin/line/create')->withErrors($errors)->withInput();
    }

    $new_processes = Input::get('new_processes', array());

    if (empty($new_processes)) {
      $errors = array('Please create at least one process');
      return Redirect::to('admin/line/create')->withErrors($errors)->withInput();
    }

    $line = new $this->line;

    $line->title = Input::get('title');

    if (! $line->save()) {
      $errors = $line->errors()->all();
      return Redirect::to('admin/line/create')->withErrors($errors)->withInput();
    }

    foreach ($new_products as $product) {
      $product = $this->product->create($product);
      $product_ids[] = $product->id;
    }

    $line->products()->sync($product_ids);

    foreach ($new_processes as $process) {
      $process['line_id'] = $line->id;
      $process = $this->process->create($process);
    }

    return Redirect::to('admin/line')->with('success', "A production line <i>{$line->title}</i> is successfully created");
  }

  public function getUpdate($id) {
    $line = $this->line->with('products', 'processes')->find($id);

    if (empty($line->id)) {
      return Redirect::to('admin/line')->withErrors(array("A production line id {$line->id} could not be found"));
    }

    $products = $this->product->get();

    $line_product_ids = array();

    if (! $line->products->isEmpty()) {
      $line_product_ids = array_fetch($line->products->toArray(), 'id');
    }

    $new_products = Input::old('new_products', array());
    $new_products_table = $this->product->makeNewProductsTable($new_products);

    $new_processes = Input::old('new_processes', array());
    $processes = $line->processes->toArray();
    $new_processes = array_merge($processes, $new_processes);

    $new_processes_table = $this->process->makeNewProcessesTable($processes);

    return View::make('admins.lines.update', compact('line', 'products', 'processes', 'line_product_ids', 'new_processes_table', 'new_products_table'));
  }

  public function postUpdate($id) {
    $line = $this->line->with('processes')->find($id);

    if (empty($line->id)) {
      return Redirect::to('admin/line')->withErrors(array("A production line id {$line->id} could not be found"));
    }

    $product_ids = Input::get('product_ids', array());
    $new_products = Input::get('new_products', array());

    if (empty($product_ids) && empty($new_products)) {
      $errors = array('Please select or create at least one model');
      return Redirect::to("admin/line/$id/update")->withErrors($errors)->withInput();
    }

    $new_processes = Input::get('new_processes', array());

    if (empty($new_processes)) {
      $errors = array('Please create at least one process');
      return Redirect::to("admin/line/$id/update")->withErrors($errors)->withInput();
    }

    $line->title = Input::get('title');

    if (! $line->save()) {
      $errors = $line->errors()->all();
      return Redirect::to("admin/line/$id/update")->withErrors($errors)->withInput();
    }

    foreach ($new_products as $product) {
      $product = $this->product->create($product);
      $product_ids[] = $product->id;
    }

    $line->products()->sync($product_ids);

    $new_process_ids = array_keys($new_processes);

    foreach ($line->processes as $process) {
      if (! in_array($process->id, $new_process_ids)) {
        $process->delete();
      }
    }

    foreach ($new_processes as $id => $process) {
      $process['line_id'] = $line->id;

      $exist_process = $this->process->find($id);

      if (empty($exist_process->id)) {
        $this->process->create($process);
      }
      else {
        $exist_process->title = $process['title'];
        $exist_process->number = $process['number'];
        $exist_process->line_id = $process['line_id'];

        $exist_process->updateUniques();
      }
    }

    return Redirect::to("admin/line")->with('success', "A production line <i>{$line->title}</i> is successfully updated");
  }

  public function getDelete($id) {
    $error = array();

    $line = $this->line->find($id);

    if (empty($line->id)) {
      $errors = array("A production line id $id cannot be found");
      return Redirect::to('admin/line')->withErrors($errors);
    }

    return View::make('admins.lines.delete', compact('line'));
  }

  public function postDelete($id) {
    $error = array();

    $line = $this->line->with('processes')->find($id);

    if (empty($line->id)) {
      $errors = array("A production line id $id cannot be found");
      return Redirect::to('admin/line')->withErrors($errors);
    }

    if (! $line->delete()) {
      $errors = $line->errors()->all();
      return Redirect::to('admin/line')->withErrors($errors);
    }

    return Redirect::to("admin/line")->with('success', "A production line <i>{$line->title}</i> is successfully deleted");
  }

  // -------------------------------------------------------------
  // Actions

  public function getAccess($id) {
    $line = $this->line->with('products')->find($id);

    if (empty($line->id)) {
      return Redirect::to('admin/line')->withErrors(array("A production line id {$line->id} could not be found"));
    }
  }

  // -------------------------------------------------------------
  // Data table

  public function getDataTable() {
    $offset = Input::get('start');
    $limit = Input::get('length');

    $query = $this->line
                  ->with('products', 'processes')
                  ->skip($offset)
                  ->take($limit);

    $cols = array(
      'id',
      'title',
      'products',
      'processes',
      'created_at',
      'updated_at',
    );

    $orders = Input::get('order');

    foreach ($orders as $order) {
      $col_index = $order['column'];
      $query->orderBy($cols[$col_index], $order['dir']);
    }

    $search = Input::get('search');

    if (! empty($search['value'])) {
      $query->where('title', 'LIKE', "%{$search['value']}%");
    }

    $count_query = clone $query;

    $lines = $query->get();

    $items = array();
    foreach ($lines as $key => &$line) {
      $items[$key] = $line->toArray();

      $products_array = $line->products->toArray();
      $product_ids = array_fetch($products_array, 'title');

      $view_params = array(
        'items' => $product_ids,
        'col_num' => 3,
      );

      $items[$key]['products'] = View::make('admins.misc.multi_item_cell', $view_params)->render();

      $processes_array = $line->processes->toArray();
      $process_ids = array_fetch($processes_array, 'number');

      $view_params = array(
        'items' => $process_ids,
        'col_num' => 3,
      );

      $items[$key]['processes'] = View::make('admins.misc.multi_item_cell', $view_params)->render();

      $buttons = array(
        array(
          'url' => url("admin/line/{$line->id}/import"),
          'type' => 'success',
          'text' => 'Import',
        ),
        array(
          'url' => url("admin/line/{$line->id}/update"),
          'type' => 'warning',
          'text' => 'Edit',
        ),
        array(
          'url' => url("admin/line/{$line->id}/delete"),
          'type' => 'danger',
          'text' => 'Delete',
        ),
      );

      $items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
    }

    $response = array(
      'draw' => (int)Input::get('draw'),
      'recordsTotal' => $this->line->count(),
      'recordsFiltered' => $count_query->skip(0)->count(),
      'data' => $items,
    );

    return Response::json($response);
  }

  // -------------------------------------------------------------
  // Export & import

  public function getExport($id) {
    $line = $this->line->with('processes.users')->find($id);

    if (empty($line->id)) {
      return Redirect::to('admin/line')->withErrors(array("A production line id {$line->id} could not be found"));
    }

    $file_name = str_replace(' ', '_', $line->title) . '_' .  time() . '_' . uniqid();

    Excel::create($file_name, function($excel) use($line) {
      $excel->sheet('processes_users', function($sheet) use($line) {
        $row_number = 5;

        $users = User::with('processes')->get();

        $process_array = $line->processes->toArray();

        $process_titles = array_merge(array('', ''), array_fetch($process_array, 'title'));
        $process_numbers = array_merge(array('', ''), array_fetch($process_array, 'number'));

        $sheet->row($row_number, $process_titles);

        $last_col = $col = "A";

        for ($i = 0; $i < count($process_titles); ++$i) {
          $sheet->cells("$last_col$row_number", function($cells) use($sheet, $last_col, $row_number) {
            $cells->setAlignment('center');
            $sheet->getStyle("$last_col$row_number")->getAlignment()->setTextRotation(90);
          });

          if ($i < count($process_titles) - 1) {
            ++$last_col;
          }
        }

        $sheet->row(++$row_number, $process_numbers);

        $sheet->cells("$col$row_number:$last_col$row_number", function($cells) {
          $cells->setAlignment('center');
        });

        $sheet->freezePane("C7");

        foreach ($users as $key => $user) {
          $process_array = $user->processes->toArray();
          $user_process_ids = array_fetch($process_array, 'id');

          $row = array($user->email, "{$user->first_name} {$user->last_name}");

          foreach ($line->processes as $process) {
            $row[] = in_array($process->id, $user_process_ids) ? 'x' : '';
          }

          $sheet->row($key + $row_number + 1, $row);
        }

        //$sheet->freezePane('B' . $key + $row_number + 1);
      });
    })->download('xlsx');
  }

  public function getImport($id) {
    $line = $this->line->with('processes.users')->find($id);

    if (empty($line->id)) {
      return Redirect::to('admin/line')->withErrors(array("A production line id {$line->id} could not be found"));
    }

    return View::make('admins.lines.import', compact('line'));
  }

  public function postImport($id) {
    $a = array(NULL, NULL, NULL);
    $a = array_filter($a);

    ini_set('memory_limit', -1);
    set_time_limit(0);
    $line = $this->line->with('processes.users')->find($id);

    foreach ($line->processes as $process) {
      $process->users()->sync(array());
    }

    if (empty($line->id)) {
      return Redirect::to('admin/line')->withErrors(array("A production line id {$line->id} could not be found"));
    }

    if (! Input::hasFile('file')) {
      return Redirect::to("admin/line/$id/import")->withErrors(array("Please upload a file"));
    }

    $file = Input::file('file');

    $storage_path = storage_path('lines/import');

    if (! is_dir($storage_path)) {
      mkdir($storage_path, 0755, TRUE);
    }

    $file->move($storage_path, $file->getClientOriginalName());

    $is_first_chunk = TRUE;
    $process_titles = array();
    $process_numbers = array();

    $file_path = "$storage_path/{$file->getClientOriginalName()}";

    try {
      Excel::load($file_path, function($reader) use($id, &$is_first_chunk, &$process_titles, &$process_numbers) {
        $rows = $reader->toArray();

        $process_titles = array_filter($rows[0]);
        $process_numbers = array_filter($rows[1]);

        unset($rows[0]);
        unset($rows[1]);

        foreach ($process_titles as $key => $title) {
          $number = $process_numbers[$key];

          $process = Process::where('number', $number)->get()->first();

          if (empty($process)) {
            $process = new Process();
            $process->line_id = $id;
            $process->title = $title;
            $process->number = $number;
            $process->save();
          }
          elseif ($process->line_id != $id) {
            $count = 1;
            do {
              $process_number = $number . '_'  . $count;
              $process = Process::where('number', $process_number)->get()->first();
              ++$count;
            } while (! empty($process));
            $process = new Process();
            $process->line_id = $id;
            $process->title = $title;
            $process->number = $process_number;
            $process->save();

            $process_numbers[$key] = $process_number;
          }
          else {
            $process->users()->sync(array());
          }
        }

        $delete_processes = Process::where('line_id', $id)->whereNotIn('number', $process_numbers)->get();

        foreach ($delete_processes as $delete_process) {
          $delete_process->delete();
        }

        $rows = array_values($rows);
        $gr_codes = array();

        $groups = Sentry::findAllGroups();

        $group_id = NULL;
        foreach ($groups as $group) {
          $permissions = $group->getPermissions();
          foreach ($permissions as $name => $value) {
            if ($name == 'work_on_model_processes') {
              $group_id = $group->id;
            }
          }
        }

        foreach ($rows as $key => $row) {
          $row_data = array_filter($row);
          if (empty($row_data)) {
            throw new Exception('Import stop because an empty row was found');
          }

          if (empty($row[1])) {
            continue;
          }

          $user_fragments = explode('/', $row[2]);

          $qr_code = trim(head($user_fragments));
          $full_name = trim(last($user_fragments));

          $full_name_fragments = explode(' ', $full_name);
          $first_name = head($full_name_fragments);
          $last_name = last($full_name_fragments);

          $user = User::where('qr_code', $qr_code)->first();

          if (empty($user)) {
            $input = array(
              'email' => "$qr_code@minebea.co.th",
              'qr_code' => $qr_code,
              'password' => $qr_code,
              'first_name' => $first_name,
              'last_name' => $last_name,
              'activated' => true,
            );

            $user = Sentry::createUser($input);
            $group = Sentry::findGroupById($group_id);
            $user->addGroup($group);
          }

          unset($row[0]);
          unset($row[1]);
          unset($row[2]);

          $user_processes = $user->processes()->get()->toArray();

          $user_process_ids = array_fetch($user_processes, 'id');

          foreach ($row as $key => $col) {
            if (empty($process_numbers[$key])) {
              continue;
            }

            $process = Process::where('number', $process_numbers[$key])->first();

            if (empty($process)) {
              continue;
            }

            $index = array_search($process->id, $user_process_ids);

            if (empty($col) && $index !== FALSE) {
              unset($user_process_ids[$index]);
            }
            elseif (! empty($col) && $index === FALSE) {
              $user_process_ids[] = $process->id;
            }
          }

          Log::info('User process ids: ' . print_r($user_process_ids, TRUE));

          $user->processes()->sync($user_process_ids);
        }
      });
    }
    catch (Exception $e) {
      return Redirect::to("admin/line")->with('success', "A production line <i>{$line->title}</i>'s Excel file is successfully imported");
    }

    return Redirect::to("admin/line")->with('success', "A production line <i>{$line->title}</i>'s Excel file is successfully imported");
  }

  /*public function postImport($id) {
    $a = array(NULL, NULL, NULL);
    $a = array_filter($a);

    ini_set('memory_limit', -1);
    set_time_limit(0);
    $line = $this->line->with('processes.users')->find($id);

    if (empty($line->id)) {
      return Redirect::to('admin/line')->withErrors(array("A production line id {$line->id} could not be found"));
    }

    if (! Input::hasFile('file')) {
      return Redirect::to("admin/line/$id/import")->withErrors(array("Please upload a file"));
    }

    $file = Input::file('file');

    $storage_path = storage_path('lines/import');

    if (! is_dir($storage_path)) {
      mkdir($storage_path, 0755, TRUE);
    }

    $file->move($storage_path, $file->getClientOriginalName());

    $is_first_chunk = TRUE;
    $process_titles = array();
    $process_numbers = array();

    $file_path = "$storage_path/{$file->getClientOriginalName()}";

    try {
      Excel::filter('chunk')->load($file_path)->chunk(250, function($rows) use($id, &$is_first_chunk, &$process_titles, &$process_numbers) {
        Log::info('start new chunk');
        $rows = $rows->toArray();

        if ($is_first_chunk) {
          $process_titles = array_filter($rows[0]);
          $process_numbers = array_filter($rows[1]);

          unset($rows[0]);
          unset($rows[1]);

          foreach ($process_titles as $key => $title) {
            $number = $process_numbers[$key];

            $process = Process::where('number', $number)->first();

            if (empty($process)) {
              $process = new Process();
              $process->line_id = $id;
              $process->title = $title;
              $process->number = $number;
              $process->save();
            }
            elseif ($process->line_id != $id) {
              $count = 1;
              do {
                $process_number = $number . '_'  . $count;
                $process = Process::where('number', $process_number)->first();
                ++$count;
              } while (! empty($process));
              $process = new Process();
              $process->line_id = $id;
              $process->title = $title;
              $process->number = $process_number;
              $process->save();

              $process_numbers[$key] = $process_number;
            }
          }

          Process::where('line_id', $id)->whereNotIn('number', $process_numbers)->delete();
        }

        $is_first_chunk = FALSE;

        $rows = array_values($rows);
        foreach ($rows as $key => $row) {
          Log::info('start process row');
          $row_data = array_filter($row);
          if (empty($row_data)) {
            throw new Exception('Import stop because an empty row was found');
          }

          if (empty($row[1])) {
            continue;
          }

          $user_fragments = explode('/', $row[2]);

          $qr_code = trim(head($user_fragments));
          $full_name = trim(last($user_fragments));

          $full_name_fragments = explode(' ', $full_name);
          $first_name = head($full_name_fragments);
          $last_name = last($full_name_fragments);

          $user = User::where('qr_code', $qr_code)->first();

          if (empty($user)) {
            $input = array(
              'email' => "$qr_code@minebea.co.th",
              'qr_code' => $qr_code,
              'password' => $qr_code,
              'first_name' => $first_name,
              'last_name' => $last_name,
              'activated' => true,
            );

            $user = Sentry::createUser($input);
            $group = Sentry::findGroupByName('Engineer');
            $user->addGroup($group);
          }

          unset($row[0]);
          unset($row[1]);
          unset($row[2]);

          $user_processes = $user->processes()->get()->toArray();

          $user_process_ids = array_fetch($user_processes, 'id');

          Log::info('middle process row');

          foreach ($row as $key => $col) {
            if (empty($process_numbers[$key])) {
              continue;
            }

            $process = Process::where('number', $process_numbers[$key])->first();

            if (empty($process)) {
              continue;
            }

            $index = array_search($process->id, $user_process_ids);

            if (empty($col) && $index !== FALSE) {
              unset($user_process_ids[$index]);
            }
            elseif (! empty($col) && $index === FALSE) {
              $user_process_ids[] = $process->id;
            }
          }

          Log::info('finish process row');

          $user->processes()->sync($user_process_ids);
        }
      });
    }
    catch (Exception $e) {
      return Redirect::to("admin/line")->with('success', "A production line <i>{$line->title}</i>'s Excel file is successfully imported");
    }
  }*/

}