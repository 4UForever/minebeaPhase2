<?php
use Carbon\Carbon;

class AdminNgDetailController extends AdminBaseController
{

  	protected $process;
  	protected $ng_detail;

	public function __construct(Process $process, NgDetail $ng_detail) {
    	$this->process = $process;
    	$this->ng_detail = $ng_detail;
	}

	public function getIndex() {
		$headers = array('<input type="checkbox" id="checkAll">', 'ID', 'Title', 'Process', 'Created at', 'Updated at', '');
		$id = 'ng-detail';
		$url = url('admin/ng-detail/data-table');
		$filters = array(
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();

		return View::make('admins.ng_details.index', compact('datatable'));
	}

	public function getCreate() {
		$processes =  $this->process->get();
		$process_old = $processes->first()->id;

		return View::make('admins.ng_details.create', compact('processes', 'process_old'));
	}

	public function postCreate() {
		$this->ng_detail->process_id = Input::get('process_id');
		$this->ng_detail->title = Input::get('title');

		if (!$this->ng_detail->save()) {
			$errors = $this->ng_detail->errors()->all();
			return Redirect::to('admin/ng-detail/create')->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/ng-detail")->with('success', "A NG Detail <i>{$this->ng_detail->title}</i> is successfully created");
	}

	public function getUpdate($id) {
		$ng_detail = $this->ng_detail->find($id);

		if (empty($ng_detail->id)) {
			return Redirect::to('admin/ng-detail')->withErrors(array("A NG Detail id $id could not be found"));
		}

		$processes =  $this->process->get();
		$process_old = $ng_detail->process_id;
		return View::make('admins.ng_details.update', compact('ng_detail', 'processes', 'process_old'));
	}

	public function postUpdate($id) {
		$this->ng_detail = $this->ng_detail->find($id);

		if (empty($this->ng_detail->id)) {
			return Redirect::to('admin/ng-detail')->withErrors(array("A NG Detail id $id could not be found"));
		}

		$this->ng_detail->process_id = Input::get('process_id');
		$this->ng_detail->title = Input::get('title');

		if (!$this->ng_detail->save()) {
			$errors = $this->ng_detail->errors()->all();
			return Redirect::to("admin/ng-detail/$id/update")->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/ng-detail")->with('success', "A NG Detail <i>{$this->ng_detail->title}</i> is successfully updated");
	}

	public function getDelete($id) {
		$error = array();

		$ng_detail = $this->ng_detail->find($id);

		if (empty($ng_detail->id)) {
			$errors = array("A NG Detail id $id cannot be found");
			return Redirect::to('admin/ng-detail')->withErrors($errors);
		}

		return View::make('admins.ng_details.delete', compact('ng_detail'));
	}

	public function postDelete($id) {
		$error = array();

		$ng_detail = $this->ng_detail->find($id);

		if (empty($ng_detail->id)) {
			$errors = array("A NG Detail id $id cannot be found");
			return Redirect::to('admin/ng-detail')->withErrors($errors);
		}

		if (!$ng_detail->delete()) {
			$errors = $ng_detail->errors()->all();
			return Redirect::to('admin/ng-detail')->withErrors($errors);
		}

		return Redirect::to("admin/ng-detail")->with('success', "A NG Detail <i>{$ng_detail->name}</i> is successfully deleted");
	}

	public function getDataTable() {
		$offset = Input::get('start');
		$limit = Input::get('length');
		$columns = Input::get('columns');

		$query = $this->ng_detail->with('process')->skip($offset)->take($limit);

		$cols = array('id', 'title', 'process', 'created_at', 'updated_at',);

		$orders = Input::get('order');

		foreach ($orders as $order) {
			$col_index = $order['column'];
			$query->orderBy($cols[$col_index], $order['dir']);
		}

		$search = Input::get('search');

		/*if (!empty($search['value'])) {
			$query->where('title', 'LIKE', "%{$search['value']}%");
		}*/
		if( !empty($columns[2]['search']['value']) ){//title
			$search = $columns[2]['search']['value'];
			$query->where('title', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//process
			$search = $columns[3]['search']['value'];
			$query->whereHas('process', function ($query) use ($search) {
				$query->where('number', 'like', "%{$search}%");
			});
		}

		$count_query = clone $query;

		$ng_details = $query->get();

		$items = array();
		foreach ($ng_details as $key => & $ng_detail) {
			$items[$key] = $ng_detail->toArray();
			$items[$key]['process'] = $ng_detail->process->number;//'<span class="badge">'.$ng_detail->process->number.'</span>';
			$items[$key]['checkbox'] = "<input type=\"checkbox\" name=\"ids[]\" value=\"".$ng_detail->id."\">";

			$buttons = array(array('url' => url("admin/ng-detail/{$ng_detail->id}/update"), 'type' => 'warning', 'text' => 'Edit',), array('url' => url("admin/ng-detail/{$ng_detail->id}/delete"), 'type' => 'danger', 'text' => 'Delete',),);

			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}

		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $this->ng_detail->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);

		return Response::json($response);
	}

	function getImport(){
		return View::make('admins.ng_details.import');
	}

	function postImport(){
		if (! Input::hasFile('file')) {
			return Redirect::to("admin/ng-detail/import")->withErrors(array("Please upload a file"));
		}

		$file = Input::file('file');
		$storage_path = storage_path('imports');
		if (! is_dir($storage_path)) {
		  mkdir($storage_path, 0755, TRUE);
		}

		$file->move($storage_path, $file->getClientOriginalName());
		$file_path = "$storage_path/{$file->getClientOriginalName()}";
		//echo $file_path."<br>";
		$count_exist = 0;
		$count_success = 0;
		$count_failure = 0;
		$result = Excel::selectSheetsByIndex(0)->load($file_path, function($reader){
			$reader->ignoreEmpty();
			$reader->noHeading();
		})->get();

		$rows = $result->toArray();
		foreach($rows as $row=>$col){
			$title = trim($col[0]);
			$process = trim($col[1]);
			$created_at = (empty($col[2])) ? date("Y-m-d H:i:s"):trim($col[2]);
			$updated_at = (empty($col[3])) ? date("Y-m-d H:i:s"):trim($col[3]);
			if( !empty($title) && !empty($process) ){
				$ng_detail = NgDetail::where('title', $title)
					->whereHas('process', function ($query) use ($process) {
						$query->where('number', 'like', '%'.$process.'%');
					})
				->count();
				//Check process exists
				$process_count = Process::where('number', $process)->get()->count();
				if($process_count<1){
					$count_failure++;
				} else {
					if($ng_detail<1){
						$ng_insert = new NgDetail;
						$ng_insert->title = $title;
						$ng_insert->process_id = Process::where('number', $process)->first()->id;
						$ng_insert->created_at = $created_at;
						/*echo "<pre>";
						print_r($ng_insert->toArray());
						echo "</pre>";*/
						if( !empty($ng_insert->title) && !empty($ng_insert->process_id) ){
							$count_success++;
							$ng_insert->save();
						} else {
							$count_failure++;
						}
					} else {
						$count_exist++;
					}
				}
			} else {
				$count_failure++;
			}
		}
		return Redirect::to("admin/ng-detail")->with('success', "Excel file of NG Detail is successfully imported. <br>(Success : ".$count_success." rows, Failure : ".$count_failure." rows, Exist : ".$count_exist." rows)");
	}

	function getExport(){
		$ng_details = NgDetail::with('process')->get();
		$data = array();
		foreach($ng_details as $key=>$row){
			$data[$key] = array(
				'title' => $row->title,
				'process' => $row->process->number,
				'created_at' => $row->created_at->format('Y-m-d H:i:s'),
				'updated_at' => $row->updated_at->format('Y-m-d H:i:s')
			);
		}
		/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		*/
		Excel::create('NgDetails', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'Title', 'Process', 'Created at', 'Updated at'
				));
				$sheet->row(1, function($row) {
					$row->setFontWeight('bold');
				});
			});
		})->export('xls');
	}

	function getDeleteMulti()
	{
		$delete['rows'] = Input::get('rows');
		$delete['title'] = "NG Detail";
		$delete['url'] = url("admin/ng-detail");
		$delete['warning'] = "";
		return View::make('admins.misc.delete_multi', compact('delete'));
	}

	function getDeleteMultiConfirmed()
	{
		$rows = Input::get('rows');
		$ids = explode(",", $rows);
		$affectedRows = NgDetail::whereIn('id', $ids)->delete();

		return Redirect::to("admin/ng-detail")->with('success', "NG-Detail ID : <i>{$rows}</i> was successfully deleted");
	}

}
