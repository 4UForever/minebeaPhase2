<?php

use Carbon\Carbon;

class AdminProcessController extends AdminBaseController {

	// -------------------------------------------------------------
	// Dependencies

	protected $process;
	protected $line;
	protected $user;

	// -------------------------------------------------------------
	// Constructor

	public function __construct(Process $process, Line $line) {
		$this->process = $process;
		$this->line = $line;
		$this->user = Sentry::getUserProvider()->createModel();
	}

	// -------------------------------------------------------------
	// Configurations

	protected $limit = 20;
	protected $required_permission = 'work_on_model_processes';

	// -------------------------------------------------------------
	// Lab

	public function getLab() {
	//Artisan::call('db:seed');
	}

	// -------------------------------------------------------------
	// CRUD

	public function getIndex() {
		$headers = array('ID', 'Title', 'Number', 'Line', 'Users', 'Created at', 'Updated at', '');
		$id = 'processes';
		$url = url('admin/process/data-table');
		/*$filters = array(
			"1" => "<input type=\"text\" class=\"column_filter\" data-column=\"1\">",
			"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">"
		);*/
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers'))->render();

		return View::make('admins.processes.index', compact('datatable'));
	}

	public function getCreate() {
		$lines = $this->line->get();
		$users = Sentry::findAllUsersWithAccess($this->required_permission);
			return View::make('admins.processes.create', compact('lines', 'users'));
		}

		public function postCreate() {
		$user_ids = Input::get('user_ids', array());

		if (empty($user_ids)) {
			$errors = array('Please select at least one user');
			return Redirect::to('admin/process/create')->withErrors($errors)->withInput();
		}

		$process = new $this->process;

		$process->title = Input::get('title');
		$process->number = Input::get('number');
		$process->line_id = Input::get('line_id');

		if (! $process->save()) {
			$errors = $process->errors()->all();
			return Redirect::to('admin/process/create')->withErrors($errors)->withInput();
		}

		$process->users()->sync($user_ids);

		return Redirect::to("admin/process")->with('success', "A process <i>{$process->title}</i> is successfully created");
	}

	public function getUpdate($id) {
		$process = $this->process->with(array('line', 'users'))->find($id);

		if (empty($process->id)) {
			return Redirect::to('admin/process')->withErrors(array("A process id {$process->id} could not be found"));
		}

		$lines = $this->line->get();

		$users = Sentry::findAllUsersWithAccess($this->required_permission);

		$process_user_ids = array();

		foreach ($process->users as $process_user) {
			$process_user_ids[] = $process_user->id;
		}

		return View::make('admins.processes.update', compact('process', 'lines', 'users', 'process_user_ids'));
	}

	public function postUpdate($id) {
		$process = $this->process->with(array('line', 'users'))->find($id);

		if (empty($process->id)) {
			return Redirect::to("admin/process/$id/update")->withErrors(array("A process id {$process->id} could not be found"));
		}

		$user_ids = Input::get('user_ids', array());

		if (empty($user_ids)) {
			return Redirect::to("admin/process/$id/update")->withErrors(array("Please select at least one user"));
		}

		$process->title = Input::get('title');
		$process->number = Input::get('number');
		$process->line_id = Input::get('line_id');

		if (! $process->updateUniques()) {
			$errors = $process->errors()->all();
			return Redirect::to("admin/process/$id/update")->withErrors($errors);
		}

		$process->users()->sync($user_ids);

		return Redirect::to("admin/process")->with('success', "A process <i>{$process->title}</i> is successfully updated");
	}

	public function getDelete($id) {
		$error = array();

		$process = $this->process->find($id);

		if (empty($process->id)) {
			$errors = array("A process id $id cannot be found");
			return Redirect::to('admin/process')->withErrors($errors);
		}

		return View::make('admins.processes.delete', compact('process'));
	}

	public function postDelete($id) {
		$error = array();

		$process = $this->process->find($id);

		if (empty($process->id)) {
			$errors = array("A process id $id cannot be found");
			return Redirect::to('admin/process')->withErrors($errors);
		}

		if (! $process->delete()) {
			$errors = $process->errors()->all();
			return Redirect::to('admin/process')->withErrors($errors);
		}

		return Redirect::to("admin/process")->with('success', "A process <i>{$process->title}</i> is successfully deleted");
	}

	// -------------------------------------------------------------
	// Data table

	public function getDataTable() {
		$offset = Input::get('start');
		$limit = Input::get('length');

		$query = $this->process
						->with('line', 'users')
						->skip($offset)
						->take($limit);

		$cols = array(
			'id',
			'title',
			'number',
			'line',
			'users',
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
			$query->orWhere('number', 'LIKE', "%{$search['value']}%");
		}
		/*if( !empty($columns[1]['search']['value']) ){//number
			$search = $columns[1]['search']['value'];
			$query->where('title', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[2]['search']['value']) ){//name
			$search = $columns[2]['search']['value'];
			$query->where('number', 'LIKE', "%{$search}%");
		}*/

		$count_query = clone $query;

		$processes = $query->get();

		$items = array();
		foreach ($processes as $key => &$process) {
			$items[$key] = $process->toArray();

			$view_params = array(
			'items' => array($process->line->title),
			'col_num' => 3,
			);

			$items[$key]['line'] = View::make('admins.misc.multi_item_cell', $view_params)->render();

			$users_array = $process->users->toArray();

			$user_full_names = array();

			foreach ($users_array as $user) {
			$user_full_names[] = "{$user['first_name']} {$user['last_name']}";
			}

			$view_params = array(
			'items' => $user_full_names,
			'col_num' => 3,
			);

			$items[$key]['users'] = View::make('admins.misc.multi_item_cell', $view_params)->render();

			$buttons = array(
			array(
				'url' => url("admin/process/{$process->id}/update"),
				'type' => 'warning',
				'text' => 'Edit',
			),
			array(
				'url' => url("admin/process/{$process->id}/delete"),
				'type' => 'danger',
				'text' => 'Delete',
			),
			);

			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}

		$response = array(
			'draw' => (int)Input::get('draw'),
			'recordsTotal' => $this->process->count(),
			'recordsFiltered' => $count_query->skip(0)->count(),
			'data' => $items,
		);

		return Response::json($response);
	}

	// -------------------------------------------------------------
	// Form

	public function getCreateForm() {
		$id = Input::get('id', '');
		$title = Input::get('title', '');
		$number = Input::get('number', '');
		return View::make('admins.processes.create_form', compact('id', 'number', 'title'));
	}

	public function postCreateFormValidate() {
		$input = Input::all();

		$process = $this->process->find($input['id']);

		if (empty($process->id)) {
			$rules = array(
			'title' => 'required|max:128',
			'number' => 'required|unique:processes',
			);
		}
		elseif ($process->number == $input['number']) {
			$rules = array(
			'title' => 'required|max:128',
			);
		}
		else {
			$rules = array(
			'title' => 'required|max:128',
			'number' => 'required|unique:processes',
			);
		}

		$validator = Validator::make($input, $rules);

		if ($validator->fails()) {
			$messages = $validator->messages()->all();
			$response = View::make('admins.misc.errors', array('errors' => $messages))->render();
			return Response::json($response, 400);
		}

		$response = $this->process->makeNewProcessesTableRow($input);

		return Response::json($response);
	}

}
