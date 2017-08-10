<?php
use Carbon\Carbon;

class AdminProcessWorking extends AdminBaseController
{

  	protected $user;

	public function __construct(User $user) {
    	$this->user = $user;
	}

	public function getIndex() {
		$headers = array('ID', 'User', 'Working process-log', 'Working process', '');
		$id = 'process-working';
		$url = url('admin/process-working/data-table');
		$filters = array(
						"1" => "<input type=\"text\" class=\"column_filter\" data-column=\"1\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();

		return View::make('admins.process_working.index', compact('datatable'));
	}

	public function getDataTable() {
		$offset = Input::get('start');
		$limit = Input::get('length');
		$columns = Input::get('columns');

		$query = DB::table('users')->whereNotNull('on_process')
				->select('users.id', DB::raw('CONCAT(first_name, " ", last_name) AS full_name'), 'on_process', 'processes.number', 'processes.title')
				->join('processes', 'users.working_process', '=', 'processes.id')
				->skip($offset)->take($limit);

		//$query = $this->user->whereNotNull('on_process')->skip($offset)->take($limit);

		$cols = array('users.id', 'first_name', 'on_process', 'working_process');
		$orders = Input::get('order');
		foreach ($orders as $order) {
			$col_index = $order['column'];
			$query->orderBy($cols[$col_index], $order['dir']);
		}

		$search = Input::get('search');

		/*if (!empty($search['value'])) {
			$query->where('full_name', 'LIKE', "%{$search['value']}%");
		}*/
		if( !empty($columns[1]['search']['value']) ){//line
			$search = $columns[1]['search']['value'];
			$query->where('first_name', 'LIKE', "%{$search}%")->orWhere('last_name', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//product
			$search = $columns[3]['search']['value'];
			$query->where('processes.number', 'LIKE', "%{$search}%")->orWhere('processes.title', 'LIKE', "%{$search}%");
		}

		$count_query = clone $query;

		$process_workings = $query->get();
		//print_r($process_workings);

		$items = array();
		foreach ($process_workings as $key => & $process_working) {
			$items[$key] = (array) $process_working;
			$items[$key]['working_process'] = $process_working->title." (".$process_working->number.")";

			$buttons = array(
				array('url' => url("admin/process-log/{$process_working->on_process}/detail"), 'type' => 'warning', 'text' => 'Detail',),
				array('url' => url("admin/process-working/{$process_working->id}/clear"), 'type' => 'danger', 'text' => 'Clear',)
			);

			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}
		//print_r($items);
		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $count_query->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);

		return Response::json($response);
	}

	function getClearProcess($user_id)
	{
		$user = User::find($user_id);
		if(!empty($user->on_process)){
			$process_log = ProcessLog::find($user->on_process);
			//echo "<pre>";print_r($process_log->toArray());echo "</pre>";
			return View::make('admins.process_working.clear', compact('user', 'process_log'));
		}
	}

	function getClearProcessNormal($user_id)
	{
		$user = User::find($user_id);
		$user->on_process = NULL;
		$user->working_process = NULL;
		$user->save();
		return Redirect::to("admin/process-working")->with('success', "Successfully clear process for user <i>{$user->first_name} {$user->last_name}</i>");
	}

	function getClearProcessForce($user_id)
	{
		$user = User::find($user_id);
		if(!empty($user->on_process)){
			$process_log = ProcessLog::find($user->on_process);
			if(!empty($process_log->lot_id)){
				$lot = Lot::with('processes')->find($process_log->lot_id);
				if(!empty($lot)){
					if ($lot->delete()) {
						$process_ids = array_fetch($lot->processes->toArray(), "id");
						$lot->processes()->detach($process_ids);
					}
				}
			}
			$user->on_process = NULL;
			$user->working_process = NULL;
			$user->save();
			return Redirect::to("admin/process-working")->with('success', "Successfully clear process for user <i>{$user->first_name} {$user->last_name}</i>");
		}
	}

	function getTest()
	{
		$process_log_id = "8";
		$sum = ProcessLogNg::where('process_log_id', $process_log_id)->sum('quantity');
		$break = ProcessLogBreak::where('process_log_id', $process_log_id)->sum('total_minute');
		echo $sum." ".$break;
	}

}
