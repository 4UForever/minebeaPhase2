<?php
use Carbon\Carbon;

class AdminLotController extends AdminBaseController
{

	protected $lot;
	protected $product;
  	protected $process;
  	protected $line;

	public function __construct(Lot $lot, Product $product, Process $process, Line $line) {
		$this->lot = $lot;
		$this->product = $product;
    	$this->process = $process;
    	$this->line = $line;
	}

	public function getIndex() {
		$headers = array('ID', 'Number', 'Quantity', 'WIP (Title)', 'Processes', 'Created at', 'Updated at', '');
		$id = 'lots';
		$url = url('admin/lot/data-table');
		$filters = array(
						"1" => "<input type=\"text\" class=\"column_filter\" data-column=\"1\">",
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();

		return View::make('admins.lots.index', compact('datatable'));
	}

	public function getDataTable() {
		$offset = Input::get('start', 0);
		$limit = Input::get('length', 10);
		$columns = Input::get('columns');

		$query = $this->lot->with(array('processes' => function($query) {
					$query->orderBy('sort');
				}))->skip($offset)->take($limit);

		$cols = array('id', 'number', 'quantity', 'wip_title', 'processes', 'created_at', 'updated_at',);

		$orders = Input::get('order');

		foreach ($orders as $order) {
			$col_index = $order['column'];
			$query->orderBy($cols[$col_index], $order['dir']);
		}

		$search = Input::get('search');

		/*if (!empty($search['value'])) {
			$query->where('number', 'LIKE', "%{$search['value']}%");
		}*/
		if( !empty($columns[1]['search']['value']) ){//number
			$search = $columns[1]['search']['value'];
			$query->where('number', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[2]['search']['value']) ){//quantity
			$search = $columns[2]['search']['value'];
			$query->where('quantity', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//wip
			$search = $columns[3]['search']['value'];
			$query->whereHas('wip', function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%");
			});
		}

		$count_query = clone $query;

		$lots = $query->get();

		$items = array();
		foreach ($lots as $key => & $lot) {
			$items[$key] = $lot->toArray();

			$process_array = $lot->processes->toArray();
			$items[$key]['processes'] = View::make('admins.lots.multi_item_cell', compact('process_array'))->render();

			$buttons = array(
				array('url' => url("admin/lot/{$lot->id}/detail"), 'type' => 'warning', 'text' => 'Detail',),
				array('url' => url("admin/lot/{$lot->id}/delete"), 'type' => 'danger', 'text' => 'Delete',)
			);
			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}

		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $this->lot->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);

		return Response::json($response);
	}

	public function getDetail($id) {
		$lot = $this->lot->with(array('processes' => function($query) {
					$query->orderBy('sort');
				}))->find($id);

		if (empty($lot->id)) {
			return Redirect::to('admin/lot')->withErrors(array("A Lot id $id could not be found"));
		}

		return View::make('admins.lots.detail', compact('lot'));
	}

	function getDelete($id){
		$lot = Lot::find($id);

		/*$lot_working = DB::table('lot_process')->where('lot_id', $id)->whereNotNull('process_log_id')->whereNull('qty')->first();
		if(!empty($lot_working)){
			return Redirect::to('admin/lot')->withErrors(array("Lot number ".$lot->number." is on working by process ID : ".$lot_working->process_id));
		} else {*/
			return View::make('admins.lots.delete', compact('lot'));
		// }
	}

	function getDeleteConfirmed($id){
		$lot = Lot::with('processes')->find($id);
		// echo "<pre>";print_r($lot->processes->toArray());echo "</pre>";
		if(count($lot->processes->toArray())>0){
			$arr_process = [];
			foreach($lot->processes as $p){
				if(!empty($p->pivot->process_log_id)){
					$arr_process[] = $p->pivot->process_log_id;
				}
			}
			// print_r($arr_process);
			DB::table('process_logs')->whereIn('id', $arr_process)->delete();
			DB::table('process_log_breaks')->whereIn('process_log_id', $arr_process)->delete();
			DB::table('process_log_inputs')->whereIn('process_log_id', $arr_process)->delete();
			DB::table('process_log_ng1s')->whereIn('process_log_id', $arr_process)->delete();
			DB::table('process_log_ngs')->whereIn('process_log_id', $arr_process)->delete();
			DB::table('process_log_parts')->whereIn('process_log_id', $arr_process)->delete();
		}
		/*$lot_working = DB::table('lot_process')->where('lot_id', $id)->whereNotNull('process_log_id')->whereNull('qty')->first();
		if(!empty($lot_working)){
			return Redirect::to('admin/lot')->withErrors(array("Lot number ".$lot->number." is on working by process ID : ".$lot_working->process_id));
		} else {*/
			if ($lot->delete()) {
				$process_ids = array_fetch($lot->processes->toArray(), "id");
				$lot->processes()->detach($process_ids);
			}
			return Redirect::to("admin/lot")->with('success', "Successfully delete lot <i>{$lot->number}</i>");
		/*}*/
	}

	public function getTest(){
		$query = $this->lot->with(array('processes' => function($query) {
					$query->orderBy('sort');
				}))->skip(0)->take(10);
		$lots = $query->get();
		/*echo "<pre>";
		print_r($lots->toArray());
		echo "</pre>";*/
		echo "<br>";
		foreach ($lots as $key => & $lot) {
			$items[$key] = $lot->toArray();
			$items[$key]['wip'] = $lot->wip->title;

			$process_array = $lot->processes->toArray();
			return View::make('admins.lots.multi_item_cell', compact('process_array'));
			//print_r($process_array);
			/*$process_ids = array_fetch($process_array, 'number');
			$view_params = array(
			'items' => $process_ids,
			'col_num' => 3,
			);
			$items[$key]['processes'] = View::make('admins.lots.multi_item_cell', $view_params)->render();*/
		}
		//print_r($items);
	}

}
