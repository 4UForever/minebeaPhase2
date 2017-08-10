<?php
use Carbon\Carbon;

class AdminWipController extends AdminBaseController
{

	protected $lot;
	protected $product;
  	protected $process;
  	protected $line;

	public function __construct(Lot $lot, Product $product, Process $process, Line $line, Wip $wip) {
		$this->lot = $lot;
		$this->product = $product;
    	$this->process = $process;
    	$this->line = $line;
    	$this->wip = $wip;
	}

	public function getIndex() {
		$headers = array('ID', 'Title', 'Line', 'Product', 'Processes (Number)', 'Created at', 'Updated at', '');
		$id = 'wips';
		$url = url('admin/wip/data-table');
		$filters = array(
						"1" => "<input type=\"text\" class=\"column_filter\" data-column=\"1\">",
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">",
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();

		return View::make('admins.wips.index', compact('datatable'));
	}

	public function getCreate() {
		$lines =  $this->line->with('products', 'processes')->get();
		$line_id = $lines->first()->id;

		$products = $lines->first()->products;
		$product_old = $products->first()->id;
		$product_select = View::make('admins.wips.product_select', compact('products', 'product_old'))->render();

		return View::make('admins.wips.create', compact('lines', 'product_select', 'process_select'));
	}

	public function postCreate() {
		$this->wip->line_id = Input::get('line_id');
		$this->wip->product_id = Input::get('product_id');
		$this->wip->title = Input::get('title');

		if (!$this->wip->save()) {
			$errors = $this->wip->errors()->all();
			return Redirect::to('admin/wip/create')->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/wip")->with('success', "A WIP <i>{$this->wip->title}</i> is successfully created");
	}

	public function getUpdate($id) {
		$wip = $this->wip->find($id);

		if (empty($wip->id)) {
			return Redirect::to('admin/wip')->withErrors(array("A WIP id $id could not be found"));
		}

		$processes = $this->process->where('line_id', $wip->line_id)->get();
		return View::make('admins.wips.update', compact('wip', 'processes'));
	}

	public function postUpdate($id) {
		$this->wip = $this->wip->find($id);

		if (empty($this->wip->id)) {
			return Redirect::to('admin/wip')->withErrors(array("A WIP id $id could not be found"));
		}

		$this->wip->title = Input::get('title');
		if (!$this->wip->save()) {
			$errors = $this->wip->errors()->all();
			return Redirect::to("admin/wip/$id/update")->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/wip")->with('success', "A WIP <i>{$this->wip->title}</i> is successfully updated");
	}

	public function getDelete($id) {
		$error = array();

		$wip = $this->wip->find($id);

		if (empty($wip->id)) {
			$errors = array("A WIP id $id cannot be found");
			return Redirect::to('admin/wip')->withErrors($errors);
		}

		return View::make('admins.wips.delete', compact('wip'));
	}

	public function postDelete($id) {
		$error = array();

		$wip = $this->wip->find($id);

		if (empty($wip->id)) {
			$errors = array("A wip id $id cannot be found");
			return Redirect::to('admin/wip')->withErrors($errors);
		}

		if ($wip->delete()) {
			$wip_id = $wip->id;
			$processes = $this->process->whereHas('wips', function($q) use($wip_id) {
				$q->where('wip_id', $wip_id);
			})->get();
			$process_ids = array_fetch($processes->toArray(), "id");
			$wip->processes()->detach($process_ids);
		} else {
			$errors = $wip->errors()->all();
			return Redirect::to('admin/wip')->withErrors($errors);
		}

		return Redirect::to("admin/wip")->with('success', "A wip <i>{$wip->title}</i> is successfully deleted");
	}

	public function getDataTable() {
		$offset = Input::get('start', 0);
		$limit = Input::get('length', 10);
		$columns = Input::get('columns');

		$query = $this->wip->with(array('processes' => function($query) {
					$query->orderBy('sort');
				}))->skip($offset)->take($limit);

		$cols = array('id', 'title', 'line', 'product', 'processes', 'created_at', 'updated_at',);

		$orders = Input::get('order');

		foreach ($orders as $order) {
			$col_index = $order['column'];
			$query->orderBy($cols[$col_index], $order['dir']);
		}

		$search = Input::get('search');

		/*if (!empty($search['value'])) {
			$query->where('title', 'LIKE', "%{$search['value']}%");
		}*/
		if( !empty($columns[1]['search']['value']) ){//title
			$search = $columns[1]['search']['value'];
			$query->where('title', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[2]['search']['value']) ){//line
			$search = $columns[2]['search']['value'];
			$query->whereHas('line', function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%");
			});
		}
		if( !empty($columns[3]['search']['value']) ){//model
			$search = $columns[3]['search']['value'];
			$query->whereHas('product', function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%");
			});
		}

		$count_query = clone $query;

		$wips = $query->get();

		$items = array();
		foreach ($wips as $key => & $wip) {
			$items[$key] = $wip->toArray();

			$items[$key]['line'] = $wip->line->title;
			$items[$key]['product'] = $wip->product->title;

			$process_array = $wip->processes->toArray();
			$process_ids = array_fetch($process_array, 'number');
			$view_params = array(
			'items' => $process_ids,
			'col_num' => 3,
			);
			$items[$key]['processes'] = View::make('admins.misc.multi_item_cell', $view_params)->render();

			$buttons = array(array('url' => url("admin/wip/{$wip->id}/processes"), 'type' => 'success', 'text' => 'Processes',), array('url' => url("admin/wip/{$wip->id}/update"), 'type' => 'warning', 'text' => 'Edit',), array('url' => url("admin/wip/{$wip->id}/delete"), 'type' => 'danger', 'text' => 'Delete',),);
			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}

		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $this->wip->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);

		return Response::json($response);
	}

	function getProductSelect(){
		$line_id = Input::get('line_id');
		$products = $this->product->whereHas('lines', function($q) use ($line_id) {
						$q->where('line_id', $line_id);
					})->get();
		$product_old = isset($products->first()->id) ? $products->first()->id:"0";
		return View::make('admins.wips.product_select', compact('products', 'product_old'));
	}

	function getProcesses($id)
	{
		$wip = $this->wip->find($id);

		if (empty($wip->id)) {
			return Redirect::to('admin/wip')->withErrors(array("A WIP id $id could not be found"));
		}

		$wip_processes = $this->wip->with('processes')->where('line_id', $wip->line_id)->where('product_id', $wip->product_id)->get();
		$wip_process_ids = array();
		foreach($wip_processes as $wps){
			foreach($wps->processes as $wp){
				array_push($wip_process_ids, $wp->id);
			}
		}
		$processes = $this->process
					->where('line_id', $wip->line_id)
					->whereNotIn('id', $wip_process_ids)->get();
		//echo "<pre>";print_r($processes->toArray());echo "</pre>";
		$process_old = ($processes->isEmpty()) ? "":$processes->first()->id;
		return View::make('admins.wips.manage_process', compact('wip', 'processes', 'process_old'));
	}

	function postProcesses($id)//Add processes
	{
		$lot = Lot::where('wip_id', $id)->whereNull('quantity')->first();
		if(!empty($lot)){
			return Redirect::to("admin/wip/$id/processes")->withErrors(array("Failure!, Lot number ".$lot->number." is working on this condition."));
		}

		$process_id = Input::get('process_id');
		$process_number = $this->process->find($process_id)->number;

		$wip = $this->wip->find($id);
		$wip_line_product = $this->wip->where('line_id', $wip->line_id)->where('product_id', $wip->product_id)->get();
		$wip_ids = array_fetch($wip_line_product->toArray(), "id");

		$processes_wip = $this->process->where('id', $process_id)
			->whereHas('wips', function($q) use($wip_ids){
				$q->whereIn('wip_id', $wip_ids);
			})->get();
		//echo "<pre>";print_r($processes_wip->toArray());echo "</pre>";

		if($processes_wip->count()>0){
			return Redirect::to("admin/wip/$id/processes")->withErrors(array("A Process number $process_number has exists in WIP (Line:".$wip->line->title.", Model:".$wip->product->title.")"));
		} else {
			$wip_process = $this->wip->with('processes')->find($id);
			$max = $wip_process->processes->count();
			$sort = $max+1;

			$wip_pivot = $this->wip->find($id);
			$wip_pivot->processes()->attach($process_id, array('sort'=>$sort));

			return Redirect::to("admin/wip/$id/processes")->with('success', "A Process number <i>$process_number</i> is successfully added to WIP id ".$id);
		}
	}

	function detachProcess($id, $process_id)//Remove processes
	{
		$lot = Lot::where('wip_id', $id)->whereNull('quantity')->first();
		if(!empty($lot)){
			return Redirect::to("admin/wip/$id/processes")->withErrors(array("Failure!, Lot number ".$lot->number." is working on this condition."));
		}

		$process_number = $this->process->find($process_id)->number;

		//update sort value
		$query_wip = DB::table('wip_process')->where('wip_id', $id)->where('process_id', $process_id)->first();
		$wip = (array) $query_wip;
		$query_update = DB::table('wip_process')->where('wip_id', $id)->where('sort', '>', $wip['sort'])->decrement('sort');
		$wip_update = (array) $query_update;
		//detach pivot record
		$wip_pivot = $this->wip->find($id);
		$wip_pivot->processes()->detach($process_id);

		return Redirect::to("admin/wip/$id/processes")->with('success', "A Process number <i>$process_number</i> is successfully delete from WIP id ".$id);
	}

	function getTest()
	{
		$offset = Input::get('start', 0);
		$limit = Input::get('length', 10);
		$query = $this->wip->with('line', 'product', 'processes')->skip($offset)->take($limit);
		$wips = $query->get();
		//$query = $this->process->with('users')->skip(0)->take(10)->get();
		echo "<pre>";
		print_r($wips->toArray());
		echo "</pre>";
	}

}
