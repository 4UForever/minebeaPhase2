<?php
use Carbon\Carbon;

class AdminProcessLogController extends AdminBaseController
{

  	protected $process_log;
  	protected $process_log_break;
  	protected $process_log_input;

	public function __construct(ProcessLog $process_log, ProcessLogBreak $process_log_break, ProcessLogInput $process_log_input) {
    	$this->process_log = $process_log;
    	$this->process_log_break = $process_log_break;
    	$this->process_log_input = $process_log_input;
	}

	public function getIndex() {
		$headers = array('<input type="checkbox" id="checkAll">', 'ID', 'User', 'Line', 'Process number', 'Process title', 'Model', 'Lot', 'Leader', 'Start time', 'End time', 'Total minute', 'OK Qty', 'NG Qty', 'Total Break', 'First S/N', 'Last S/N', 'Detail');
		$id = 'process-log';
		$url = url('admin/process-log/data-table');
		$filters = array(
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">",
						"4" => "<input type=\"text\" class=\"column_filter\" data-column=\"4\">",
						"5" => "<input type=\"text\" class=\"column_filter\" data-column=\"5\">",
						"6" => "<input type=\"text\" class=\"column_filter\" data-column=\"6\">",
						"7" => "<input type=\"text\" class=\"column_filter\" data-column=\"7\">",
						"8" => "<input type=\"text\" class=\"column_filter\" data-column=\"8\">",
						"9" => "<input type=\"text\" class=\"column_filter\" data-column=\"9\">",
						"10" => "<input type=\"text\" class=\"column_filter\" data-column=\"10\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();

		return View::make('admins.process_logs.index', compact('datatable'));
	}

	public function getDetail($id) {
		$process_log = $this->process_log->find($id);

		if (empty($process_log->id)) {
			return Redirect::to('admin/process-log')->withErrors(array("A Process log id $id could not be found"));
		}

		$processes_log_break =  ProcessLogBreak::where('process_log_id', $process_log->id)->get();
		$process_log_part = ProcessLogPart::where('process_log_id', $process_log->id)->get();
		$process_log_input['IQC'] = ProcessLogInput::where('process_log_id', $process_log->id)->where('lot_type', 'IQC')->get();
		$process_log_input['WIP'] = ProcessLogInput::where('process_log_id', $process_log->id)->where('lot_type', 'WIP')->get();
		$process_log_ng = ProcessLogNg::where('process_log_id', $process_log->id)->get();
		return View::make('admins.process_logs.detail', compact('process_log', 'processes_log_break', 'process_log_input', 'process_log_ng', 'process_log_part'));
	}

	public function getDataTable() {
		$offset = Input::get('start');
		$limit = Input::get('length');
		$columns = Input::get('columns');

		$query = $this->process_log->skip($offset)->take($limit);

		$cols = array('id', 'full_name', 'line_title', 'process_number', 'process_title', 'product_title', 'lot_number', 'line_leader_name', 'start_time', 'end_time', 'total_minute', 'ok_qty', 'ng_qty', 'total_break', 'first_serial_no', 'last_serial_no');

		$orders = Input::get('order');

		foreach ($orders as $order) {
			$col_index = $order['column'];
			$query->orderBy($cols[$col_index], $order['dir']);
		}

		$search = Input::get('search');

		if (!empty($search['value'])) {
			if($search['value']==2){
				$query->whereNotNull('end_time');
			} else if($search['value']==3){
				$query->whereNull('end_time');
			}
		}
		if( !empty($columns[2]['search']['value']) ){//user
			$search = $columns[2]['search']['value'];
			$query->where('full_name', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//line
			$search = $columns[3]['search']['value'];
			$query->where('line_title', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[4]['search']['value']) ){//product
			$search = $columns[4]['search']['value'];
			$query->where('process_number', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[5]['search']['value']) ){//product
			$search = $columns[5]['search']['value'];
			$query->where('process_title', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[6]['search']['value']) ){//process
			$search = $columns[6]['search']['value'];
			$query->where('product_title', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[7]['search']['value']) ){//lot
			$search = $columns[7]['search']['value'];
			$query->where('lot_number', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[8]['search']['value']) ){//leader
			$search = $columns[8]['search']['value'];
			$query->where('line_leader_name', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[9]['search']['value']) ){//leader
			$search = $columns[9]['search']['value'];
			$query->where('start_time', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[10]['search']['value']) ){//leader
			$search = $columns[10]['search']['value'];
			$query->where('end_time', 'LIKE', "%{$search}%");
		}

		$count_query = clone $query;

		$process_logs = $query->get();

		$items = array();
		foreach ($process_logs as $key => & $process_log) {
			$items[$key] = $process_log->toArray();
			$items[$key]['checkbox'] = "<input type=\"checkbox\" name=\"ids[]\" value=\"".$process_log->id."\">";

			$buttons = array(array('url' => url("admin/process-log/{$process_log->id}/detail"), 'type' => 'warning', 'text' => 'Detail',));

			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}

		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $this->process_log->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);

		return Response::json($response);
	}

	function getExport(){
		$process_logs = ProcessLog::all();
		$data = array();
		foreach($process_logs as $key=>$row){
			$data[$key] = array(
				'id' => $row->id,
				'full_name' => $row->full_name,
				'line_title' => $row->line_title,
				'process_number' => $row->process_number,
				'process_title' => $row->process_title,
				'product_title' => $row->product_title,
				'lot_number' => $row->lot_number,
				'line_leader_name' => $row->line_leader_name,
				'start_time' => $row->start_time,
				'end_time' => $row->end_time,
				'total_minute' => (empty($row->total_minute)) ? "":number_format($row->total_minute),
				'ok_qty' => (empty($row->ok_qty)) ? "":number_format($row->ok_qty),
				'ng_qty' => (empty($row->ng_qty)) ? "":number_format($row->ng_qty),
				'total_break' => (empty($row->total_break)) ? "":number_format($row->total_break),
				'first_serial_no' => $row->first_serial_no,
				'last_serial_no' => $row->last_serial_no
			);
		}
		/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		*/
		Excel::create('ProcessLogs', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'ID', 'User', 'Line', 'Process number', 'Process title', 'Model', 'Lot', 'Leader', 'Start time', 'End time', 'Total minute', 'OK Qty', 'NG Qty', 'Total Break', 'First S/N', 'Last S/N'
				));
				$sheet->row(1, function($row) {
					$row->setFontWeight('bold');
				});
			});
		})->export('xls');
	}

	function getExportSelected()
	{
		$rows = Input::get('rows');
		$ids = explode(",", $rows);
		//print_r($ids);
		$process_logs = ProcessLog::whereIn('id', $ids)->get();
		$data = array();
		foreach($process_logs as $key=>$row){
			$data[$key] = array(
				'id' => $row->id,
				'full_name' => $row->full_name,
				'line_title' => $row->line_title,
				'process_number' => $row->process_number,
				'process_title' => $row->process_title,
				'product_title' => $row->product_title,
				'lot_number' => $row->lot_number,
				'line_leader_name' => $row->line_leader_name,
				'start_time' => $row->start_time,
				'end_time' => $row->end_time,
				'total_minute' => (empty($row->total_minute)) ? "":number_format($row->total_minute),
				'ok_qty' => (empty($row->ok_qty)) ? "":number_format($row->ok_qty),
				'ng_qty' => (empty($row->ng_qty)) ? "":number_format($row->ng_qty),
				'total_break' => (empty($row->total_break)) ? "":number_format($row->total_break),
				'first_serial_no' => $row->first_serial_no,
				'last_serial_no' => $row->last_serial_no
			);
		}
		/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		*/
		Excel::create('ProcessLogs', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'ID', 'User', 'Line', 'Process number', 'Process title', 'Model', 'Lot', 'Leader', 'Start time', 'End time', 'Total minute', 'OK Qty', 'NG Qty', 'Total Break', 'First S/N', 'Last S/N'
				));
				$sheet->row(1, function($row) {
					$row->setFontWeight('bold');
				});
			});
		})->export('xls');
	}

	function getExportBreak()
	{
		$rows = Input::get('rows');
		$ids = explode(",", $rows);
		//print_r($ids);
		$logs_break = ProcessLogBreak::whereIn('process_log_id', $ids)->get();
		$data = array();
		foreach($logs_break as $key=>$row){
			$data[$key] = array(
				'lot' => ProcessLog::find($row->process_log_id)->lot_number,
				'process_log_start' => ProcessLog::find($row->process_log_id)->start_time,
				'process_log_end' => ProcessLog::find($row->process_log_id)->end_time,
				'process_number' => ProcessLog::find($row->process_log_id)->process_number,
				'process_title' => ProcessLog::find($row->process_log_id)->process_title,
				'process_log_id' => $row->process_log_id,
				'break_code' => $row->break_code,
				'break_reason' => $row->break_reason,
				'start_break' => $row->start_break,
				'end_break' => $row->end_break,
				'total_minute' => $row->total_minute
			);
		}
		/*echo "<pre>";
		print_r($data);
		echo "</pre>";*/
		Excel::create('ProcessLogs_Break', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'Lot', 'Start time', 'End time', 'Process number', 'Process title', 'Process log ID', 'Break code', 'Break reason', 'Start break', 'End break', 'Total minute'
				));
				$sheet->row(1, function($row) {
					$row->setFontWeight('bold');
				});
			});
		})->export('xls');
	}

	function getExportNg()
	{
		$rows = Input::get('rows');
		$ids = explode(",", $rows);
		//print_r($ids);
		$logs_ng = ProcessLogNg::whereIn('process_log_id', $ids)->orderBy('process_log_id')->get();
		$data = array();
		foreach($logs_ng as $key=>$row){
			$data[$key] = array(
				'lot' => ProcessLog::find($row->process_log_id)->lot_number,
				'process_log_start' => ProcessLog::find($row->process_log_id)->start_time,
				'process_log_end' => ProcessLog::find($row->process_log_id)->end_time,
				'process_number' => ProcessLog::find($row->process_log_id)->process_number,
				'process_title' => ProcessLog::find($row->process_log_id)->process_title,
				'process_log_id' => $row->process_log_id,
				'ng_id' => $row->id,
				'ng_title' => $row->ng_title,
				'quantity' => $row->quantity
			);
		}
		/*echo "<pre>";
		print_r($data);
		echo "</pre>";*/
		Excel::create('ProcessLogs_NG', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'Lot', 'Start time', 'End time', 'Process number', 'Process title', 'Process log ID', 'NG ID', 'NG Title', 'Quantity'
				));
				$sheet->row(1, function($row) {
					$row->setFontWeight('bold');
				});
			});
		})->export('xls');
	}

	function getExportInput()
	{
		$rows = Input::get('rows');
		$ids = explode(",", $rows);
		//print_r($ids);
		$process_logs = ProcessLogInput::whereIn('process_log_id', $ids)->get();
		$data = array();
		foreach($process_logs as $key=>$row){
			$part = Part::find($row->part_id);
			$part_number = (empty($part)) ? null:$part->number;
			$data[$key] = array(
				'lot' => ProcessLog::find($row->process_log_id)->lot_number,
				'process_log_start' => ProcessLog::find($row->process_log_id)->start_time,
				'process_log_end' => ProcessLog::find($row->process_log_id)->end_time,
				'process_number' => ProcessLog::find($row->process_log_id)->process_number,
				'process_title' => ProcessLog::find($row->process_log_id)->process_title,
				'process_log_id' => $row->process_log_id,
				'part_id' => $row->part_id,
				'part_number' => $part_number,
				'type' => $row->lot_type,
				'lot_id' => $row->lot_id,
				'lot_number' => $row->lot_number,
				'use_qty' => $row->use_qty
			);
		}
		/*echo "<pre>";
		print_r($data);
		echo "</pre>";*/
		Excel::create('ProcessLogs_Input', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'Lot', 'Start time', 'End time', 'Process number', 'Process title', 'Process log ID', 'Part ID', 'Part number', 'Type', 'Lot ID', 'Lot number', 'Use Quantity'
				));
				$sheet->row(1, function($row) {
					$row->setFontWeight('bold');
				});
			});
		})->export('xls');
	}

	function getTest()
	{
		$process_log_id = "8";
		$sum = ProcessLogNg::where('process_log_id', $process_log_id)->sum('quantity');
		$break = ProcessLogBreak::where('process_log_id', $process_log_id)->sum('total_minute');
		echo $sum." ".$break;
	}

}
