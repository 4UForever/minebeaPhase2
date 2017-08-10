<?php

use Carbon\Carbon;

class AdminUserController extends AdminBaseController {

  // -------------------------------------------------------------
  // Dependancies

  protected $user;
  protected $process;

  // -------------------------------------------------------------
  // Constructor

  public function __construct(Process $process) {
    $this->process = $process;
    $this->user = Sentry::getUserProvider()->createModel();
  }

  // -------------------------------------------------------------
  // Testing

  public function test() {

  }

  // -------------------------------------------------------------
  // CRUD

  public function getIndex() {
    $headers = array('ID', 'Email', 'Group', 'First name', 'Last name', 'Last login', 'Processes (number)', 'Leader', 'Created at', 'Updated at', '');
    $id = 'users';
    $url = url('admin/user/data-table');
    $datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers'))->render();

    return View::make('admins.users.index', compact('datatable'));
  }

  public function getCreate() {
    $groups = Sentry::findAllGroups();
    $processes = $this->process->get();

    return View::make('admins.users.create', compact('groups', 'processes'));
  }

  public function postCreate() {
    $errors = array();

    $process_ids = Input::get('process_ids');

    if (empty($process_ids)) {
      $errors[] = 'Please select at least one process.';
      return Redirect::to('admin/user/create')->withErrors($errors)->withInput();
    }

    $password = Input::get('password', NULL);
    $password_confirmation = Input::get('password_confirmation', NULL);

    if ($password != $password_confirmation) {
      $errors[] = 'Password and password confirmation does not match.';
      return Redirect::to('admin/user/create')->withErrors($errors)->withInput();
    }

    $group_id = Input::get('group_id');

    try {
      $input = array(
        'email'     => Input::get('email', NULL),
        'password'  => Input::get('password', NULL),
        'first_name'  => Input::get('first_name', NULL),
        'last_name'  => Input::get('last_name', NULL),
        'qr_code'  => Input::get('qr_code', NULL),
        'leader'  => Input::get('leader', 0),
        'activated' => true,
      );

      if (empty($input['first_name']) || empty($input['last_name'])) {
        throw new Exception('Both first name and last name are required', 400);
      }

      if (empty($input['qr_code'])) {
        throw new Exception('QR code is required', 400);
      }

      $user = Sentry::createUser($input);

      $group = Sentry::findGroupById($group_id);

      $user->addGroup($group);
      $user->processes()->sync($process_ids);

      return Redirect::to('admin/user')->with('success', "A user <i>{$user->email}</i> is successfully created");
    }
    catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
       $errors[] = 'Login field is required.';
    }
    catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
       $errors[] = 'Password field is required.';
    }
    catch (Cartalyst\Sentry\Users\UserExistsException $e) {
       $errors[] = 'User with this login already exists.';
    }
    catch (Cartalyst\Sentry\Groups\GroupNotFoundException $e) {
       $errors[] = 'Group was not found.';
    }
    catch (Exception $e) {
       $errors[] = $e->getMessage();
    }

    return Redirect::to('admin/user/create')->withErrors($errors)->withInput();
  }

  public function getUpdate($id) {
    $user = Sentry::findUserById($id);
    $groups = Sentry::findAllGroups();
    $processes = $this->process->get();

    $user_group = $user->getGroups()->first();
    $user_process_ids = array_fetch($user->processes()->get()->toArray(), 'id');

    if (empty($user->id)) {
      return Redirect::to('users')->withErrors(array("A user id <i>{$user->id}</i> could not be found."));
    }

    return View::make('admins.users.update', compact('user', 'groups', 'processes', 'user_group', 'user_process_ids'));
  }

  public function postUpdate($id) {
    $errors = array();

    $process_ids = Input::get('process_ids');

    if (empty($process_ids)) {
      $errors[] = 'Please select at least one process.';
      return Redirect::to("admin/user/$id/update")->withErrors($errors)->withInput();
    }

    $password = Input::get('password', NULL);
    $password_confirmation = Input::get('password_confirmation', NULL);

    if ($password != $password_confirmation) {
      $errors[] = 'Password and password confirmation does not match.';
      return Redirect::to("admin/user/$id/update")->withErrors($errors)->withInput();
    }

    try {
      $user = Sentry::findUserById($id);
      $group = Sentry::findGroupById(Input::get('group_id'));

      $user->email = Input::get('email');
      $user->first_name = Input::get('first_name');
      $user->last_name = Input::get('last_name');
      $user->qr_code = Input::get('qr_code');
      $user->leader = Input::get('leader', 0);

      if (!empty($password)) {
        $user->password = $password;
      }

      if (empty($user->first_name) || empty($user->last_name)) {
        throw new Exception('Both first name and last name are required', 400);
      }

      if (empty($user->qr_code)) {
        throw new Exception('QR code is required', 400);
      }

      $user->save();

      $user->removeGroup($user->getGroups()->first());
      $user->addGroup($group);
      $user->processes()->sync($process_ids);

      return Redirect::to('admin/user')->with('success', "A user {$user->first_name} {$user->last_name} is successfully updated");
    }
    catch (Cartalyst\Sentry\Users\UserExistsException $e) {
      $errors[] = array('User with this login already exists.');
    }
    catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
      $errors[] = array("A user id $id could not be found.");
    }
    catch (Exception $e) {
       $errors[] = $e->getMessage();
    }

    return Redirect::to("admin/user/$id/update")->withErrors($errors)->withInput();
  }

  public function getDelete($id) {
    $errors = array();

    try {
      $user = Sentry::findUserById($id);
      return View::make('admins.users.delete', compact('user'));
    }
    catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
      $errors[] = "A user id $id cannot be found.";
    }

    return Redirect::to('admin/user')->withErrors($errors);
  }

  public function postDelete($id) {
    $errors = array();

    try {
      $user = Sentry::findUserById($id);
      $user->processes()->sync(array());
      $user->delete();

      return Redirect::to("admin/user")->with('success', "A user <i>{$user->email}</i> is successfully deleted.");
    }
    catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
      $errors[] = "A user id <i>$id</i> cannot be found.";
    }

    return Redirect::to("admin/user/{$user->id}/delete")->withErrors($errors);
  }

  // -------------------------------------------------------------
  // Authentication

  public function getLogin() {
    if (Sentry::check()) {
      return Redirect::to('admin/group');
    }

    return View::make('admins.users.login');
  }

  public function postLogin() {
    $errors = array();

    try {
      $credentials = array(
        'email' => Input::get('email'),
        'password' => Input::get('password'),
      );

      $user = Sentry::authenticate($credentials, false);

      return Redirect::to('/');
    }
    catch (Cartalyst\Sentry\Users\LoginRequiredException $e) {
      $errors[] = 'Login field is required.';
    }
    catch (Cartalyst\Sentry\Users\PasswordRequiredException $e) {
      $errors[] = 'Password field is required.';
    }
    catch (Cartalyst\Sentry\Users\WrongPasswordException $e) {
      $errors[] = 'Wrong password, try again.';
    }
    catch (Cartalyst\Sentry\Users\UserNotFoundException $e) {
      $errors[] = 'User was not found.';
    }
    catch (Cartalyst\Sentry\Users\UserNotActivatedException $e) {
      $errors[] = 'User is not activated.';
    }
    catch (Cartalyst\Sentry\Throttling\UserSuspendedException $e) {
      $errors[] = 'User is suspended.';
    }
    catch (Cartalyst\Sentry\Throttling\UserBannedException $e) {
      $errors[] = 'User is banned.';
    }

    if (! empty($errors)) {
      return Redirect::to('admin/user/login')->withErrors($errors);
    }
  }

  public function getLogout() {
    Sentry::logout();
    return Redirect::to('admin/user/login');
  }

  // -------------------------------------------------------------
  // Data table

  public function getDataTable() {
    $offset = Input::get('start');
    $limit = Input::get('length');

    $query = $this->user
                  ->with('processes')
                  ->skip($offset)
                  ->take($limit);

    $cols = array(
      'id',
      'email',
      'groups',
      'first_name',
      'last_name',
      'last_login',
      'processes',
      'leader',
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
      $query->where('email', 'LIKE', "%{$search['value']}%");
    }

    $count_query = clone $query;

    $users = $query->get();

    $items = array();
    foreach ($users as $key => &$user) {
      $items[$key] = $user->toArray();

      $processes_array = $user->processes->toArray();
      $processes_ids = array_fetch($processes_array, 'number');

      $view_params = array(
        'items' => $processes_ids,
        'col_num' => 3,
      );

      $items[$key]['processes'] = View::make('admins.misc.multi_item_cell', $view_params)->render();

      $groups_array = $user->getGroups()->toArray();
      $group_names = array_fetch($groups_array, 'name');

      $view_params = array(
        'items' => $group_names,
        'col_num' => 3,
      );

      $items[$key]['group_names'] = View::make('admins.misc.multi_item_cell', $view_params)->render();

      $buttons = array(
        array(
          'url' => url("admin/user/{$user->id}/update"),
          'type' => 'warning',
          'text' => 'Edit',
        ),
        array(
          'url' => url("admin/user/{$user->id}/delete"),
          'type' => 'danger',
          'text' => 'Delete',
        ),
      );

      $items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
      $items[$key]['leader'] = ($user->leader=="1") ? "Leader":"Staff";
    }

    $response = array(
      'draw' => (int)Input::get('draw'),
      'recordsTotal' => $this->user->count(),
      'recordsFiltered' => $count_query->skip(0)->count(),
      'data' => $items,
    );

    return Response::json($response);
  }

}