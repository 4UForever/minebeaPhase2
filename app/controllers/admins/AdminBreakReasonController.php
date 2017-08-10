<?php
use Carbon\Carbon;

class AdminBreakReasonController extends AdminBaseController
{

	protected $break_reason;

	public function __construct(BreakReason $break_reason) {
    	$this->break = $break_reason;
	}

	public function getIndex() {
		$headers = array('<input type="checkbox" id="checkAll">', 'ID', 'Code', 'Reason', 'Created at', 'Updated at', '');
		$id = 'break';
		$url = url('admin/break/data-table');
		$filters = array(
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();

		return View::make('admins.breaks.index', compact('datatable'));
	}

	public function getCreate() {
		return View::make('admins.breaks.create');
	}

	public function postCreate() {
		$this->break->code = Input::get('code');
		$this->break->reason = Input::get('reason');

		if (!$this->break->save()) {
			$errors = $this->break->errors()->all();
			return Redirect::to('admin/break/create')->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/break")->with('success', "A Break reason <i>{$this->break->reason}</i> is successfully created");
	}

	public function getUpdate($id) {
		$break = $this->break->find($id);

		if (empty($break->id)) {
			return Redirect::to('admin/break')->withErrors(array("A Break reason id $id could not be found"));
		}

		return View::make('admins.breaks.update', compact('break'));
	}

	public function postUpdate($id) {
		$this->break = $this->break->find($id);

		if (empty($this->break->id)) {
			return Redirect::to('admin/break')->withErrors(array("A Break reason id $id could not be found"));
		}

		$this->break->code = Input::get('code');
		$this->break->reason = Input::get('reason');

		if (!$this->break->save()) {
			$errors = $this->break->errors()->all();
			return Redirect::to("admin/break/$id/update")->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/break")->with('success', "A Break reason <i>{$this->break->reason}</i> is successfully updated");
	}

	public function getDelete($id) {
		$error = array();

		$break = $this->break->find($id);

		if (empty($break->id)) {
			$errors = array("A Break reason id $id cannot be found");
			return Redirect::to('admin/break')->withErrors($errors);
		}

		return View::make('admins.breaks.delete', compact('break'));
	}

	public function postDelete($id) {
		$error = array();

		$break = $this->break->find($id);

		if (empty($break->id)) {
			$errors = array("A Break reason id $id cannot be found");
			return Redirect::to('admin/break')->withErrors($errors);
		}

		if (!$break->delete()) {
			$errors = $break->errors()->all();
			return Redirect::to('admin/break')->withErrors($errors);
		}

		return Redirect::to("admin/break")->with('success', "A Break reason <i>{$break->name}</i> is successfully deleted");
	}

	public function getDataTable() {
		$offset = Input::get('start');
		$limit = Input::get('length');
		$columns = Input::get('columns');

		$query = $this->break->skip($offset)->take($limit);

		$cols = array('id', 'code', 'reason', 'created_at', 'updated_at',);

		$orders = Input::get('order');

		foreach ($orders as $order) {
			$col_index = $order['column'];
			$query->orderBy($cols[$col_index], $order['dir']);
		}

		$search = Input::get('search');

		/*if (!empty($search['value'])) {
			$query->where('reason', 'LIKE', "%{$search['value']}%");
		}*/
		if( !empty($columns[2]['search']['value']) ){//code
			$search = $columns[2]['search']['value'];
			$query->where('code', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//reason
			$search = $columns[3]['search']['value'];
			$query->where('reason', 'LIKE', "%{$search}%");
		}

		$count_query = clone $query;

		$breaks = $query->get();
		$items = array();
		foreach ($breaks as $key => & $break) {
			$items[$key] = $break->toArray();
			$items[$key]['checkbox'] = "<input type=\"checkbox\" name=\"ids[]\" value=\"".$break->id."\">";

			$buttons = array(
				array('url' => url("admin/break/{$break->id}/update"), 'type' => 'warning', 'text' => 'Edit',),
				array('url' => url("admin/break/{$break->id}/delete"), 'type' => 'danger', 'text' => 'Delete',)
			);
			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}

		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $this->break->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);
		return Response::json($response);
	}

	function getImport(){
		return View::make('admins.breaks.import');
	}

	function postImport(){
		if (! Input::hasFile('file')) {
			return Redirect::to("admin/break/import")->withErrors(array("Please upload a file"));
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
			$code = trim($col[0]);
			$reason = trim($col[1]);
			$created_at = (empty($col[2])) ? date("Y-m-d H:i:s"):trim($col[2]);
			$updated_at = (empty($col[3])) ? date("Y-m-d H:i:s"):trim($col[3]);
			if( !empty($code) && !empty($reason) ){
				$break_reason = BreakReason::where('code', $code)->where('reason', $reason)->count();
				if($break_reason<1){
					$break_insert = new BreakReason;
					$break_insert->code = $code;
					$break_insert->reason = $reason;
					$break_insert->created_at = $created_at;
					/*echo "<pre>";
					print_r($break_insert->toArray());
					echo "</pre>";*/
					if( !empty($break_insert->code) && !empty($break_insert->reason) ){
						$count_success++;
						$break_insert->save();
					} else {
						$count_failure++;
					}
				} else {
					$count_exist++;
				}
			} else {
				$count_failure++;
			}
		}
		return Redirect::to("admin/break")->with('success', "Excel file of Break reason is successfully imported. <br>(Success : ".$count_success." rows, Failure : ".$count_failure." rows, Exist : ".$count_exist." rows)");
	}

	function getExport(){
		$breaks = BreakReason::all();
		$data = array();
		foreach($breaks as $key=>$row){
			$data[$key] = array(
				'code' => $row->code,
				'reason' => $row->reason,
				'created_at' => $row->created_at->format('Y-m-d H:i:s'),
				'updated_at' => $row->updated_at->format('Y-m-d H:i:s')
			);
		}
		/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		*/
		Excel::create('BreakReasons', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'Code', 'Reason', 'Created at', 'Updated at'
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
		$delete['title'] = "Break reason";
		$delete['url'] = url("admin/break");
		$delete['warning'] = "";
		return View::make('admins.misc.delete_multi', compact('delete'));
	}

	function getDeleteMultiConfirmed()
	{
		$rows = Input::get('rows');
		$ids = explode(",", $rows);
		$affectedRows = BreakReason::whereIn('id', $ids)->delete();

		return Redirect::to("admin/break")->with('success', "Break reason ID : <i>{$rows}</i> was successfully deleted");
	}

}
