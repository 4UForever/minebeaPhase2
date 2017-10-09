<?php
use Carbon\Carbon;

class AdminPhrase2Controller extends AdminBaseController
{
  	protected $price;
  	protected $target;
	public function __construct(ImportPrice $price, ImportTarget $target) {
    	$this->price = $price;
    	$this->target = $target;
	}

	public function importPrice(){
		$id = 'import-price';
		$url = url('admin/import-price-table');
		$headers = array('ID', 'Year', 'Month', 'Line (Title)', 'Model (Title)', 'Processes (Number)', 'Cycle Time', 'Unit Price');
		$filters = array(
						"1" => "<input type=\"text\" class=\"column_filter\" data-column=\"1\">",
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">",
						"4" => "<input type=\"text\" class=\"column_filter\" data-column=\"4\">",
						"5" => "<input type=\"text\" class=\"column_filter\" data-column=\"5\">",
						"6" => "<input type=\"text\" class=\"column_filter\" data-column=\"6\">",
						"7" => "<input type=\"text\" class=\"column_filter\" data-column=\"7\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();
		return View::make('admins.reports.price', compact('datatable'));
	}

	public function getDataPrice(){
		$offset = Input::get('start', 0);
		$limit = Input::get('length', 10);
		$columns = Input::get('columns');
		$query = $this->price->skip($offset)->take($limit);
		$cols = array('id', 'year', 'month', 'line', 'product', 'process', 'circle_time', 'unit_price');
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
			$query->where('year', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[2]['search']['value']) ){//title
			$search = $columns[2]['search']['value'];
			$query->where('month', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//line
			$search = $columns[3]['search']['value'];
			$query->whereHas('line', function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%");
			});
		}
		if( !empty($columns[4]['search']['value']) ){//model
			$search = $columns[4]['search']['value'];
			$query->whereHas('product', function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%");
			});
		}
		if( !empty($columns[5]['search']['value']) ){//model
			$search = $columns[5]['search']['value'];
			$query->whereHas('process', function ($query) use ($search) {
				$query->where('number', 'like', "%{$search}%");
			});
		}
		if( !empty($columns[6]['search']['value']) ){//title
			$search = $columns[6]['search']['value'];
			$query->where('circle_time', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[7]['search']['value']) ){//title
			$search = $columns[7]['search']['value'];
			$query->where('unit_price', 'LIKE', "%{$search}%");
		}
		// echo $query->toSql();
		$count_query = clone $query;
		$import_prices = $query->get();
		$items = array();
		foreach ($import_prices as $key => & $obj) {
			$items[$key] = $obj->toArray();
			$items[$key]['line'] = $obj->line->title;
			$items[$key]['product'] = $obj->product->title;
			$items[$key]['process'] = $obj->process->number;
		}
		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $query->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);
		return Response::json($response);
	}

	function postImportPrice(){
		if (! Input::hasFile('file')){
			return Redirect::to("admin/import-price")->withErrors(array("Please upload a file"));
		}

		$file = Input::file('file');
		$year = Input::get('year');
		$month = Input::get('month');
		$storage_path = storage_path('imports');
		if (! is_dir($storage_path)) {
			mkdir($storage_path, 0755, TRUE);
		}

		$file->move($storage_path, $file->getClientOriginalName());
		$file_path = "$storage_path/{$file->getClientOriginalName()}";

		$lines = Line::all();
		$total_false = 0;
		foreach($lines as $line){
			$res[$line->id] = $this->validatePriceByLineId($file_path, $line->id);
			if($res[$line->id]['status']===FALSE){
				$total_false++;
				return Redirect::to("admin/import-price")->withErrors(array($res[$line->id]['message']));
			}
		}
		if($total_false < 1){
			foreach($lines as $line){
				if(!empty($res[$line->id]['data'])){
					$this->insertPriceByLineId($line->id, $year, $month, $res[$line->id]['data']);
				}
			}
			return Redirect::to("admin/import-price")->with('success', "Excel file of report is successfully imported.");
		}
	}

	function validatePriceByLineId($file_path="", $line_id=""){
		$res = array("status"=>TRUE, "message"=>"");
		$col_arr = array("0"=>"A", "1"=>"B", "2"=>"C", "3"=>"D", "4"=>"E", "5"=>"F", "6"=>"G", "7"=>"H", "8"=>"I", "9"=>"J", "10"=>"K", "11"=>"L", "12"=>"M", "13"=>"N", "14"=>"O", "15"=>"P");
		try {
			$data = array();
			$result = Excel::selectSheets("line".$line_id)->load($file_path, function($reader){
				$reader->noHeading();
				$reader->ignoreEmpty();
			})->get();
			foreach($result as $row=>$col){
				$process_number = trim($col[0]);
				$process_name = trim($col[1]);
				$model = trim($col[2]);
				$cycle_time = trim($col[3]);
				$unit_price = trim($col[4]);
				if(!empty($process_number) && !empty($model)){//ignore empty rows
					$checkProcess = Process::where('number', $process_number)->first(array('id'));
					if( empty($checkProcess) && $res['status']===TRUE ){
						$res = array("status"=>FALSE, "message"=>"Error in line:".$line_id.", row:".($row+2).", column:".$col_arr[0]." (Not found process number:".$process_number." in database)");
					} else {
						$data[$row]['process_id'] = $checkProcess->id;
					}
					$checkProduct = Product::where('title', $model)->first(array('id'));
					if( empty($checkProduct) && $res['status']===TRUE ){
						$res = array("status"=>FALSE, "message"=>"Error in line:".$line_id.", row:".($row+2).", column:".$col_arr[2]." (Not found model title:".$model." in database)");
					} else {
						$data[$row]['product_id'] = $checkProduct->id;
					}
					if(!empty($cycle_time) && !is_numeric($cycle_time) && $res['status']===TRUE){
						$res = array("status"=>FALSE, "message"=>"Error in line:".$line_id.", row:".($row+2).", column:".$col_arr[3]." (cycle time is not numeric value)");
					} else {
						$data[$row]['cycle_time'] = empty($cycle_time)? "0":$cycle_time;
					}
					if(!empty($unit_price) && !is_numeric($unit_price) && $res['status']===TRUE){
						$res = array("status"=>FALSE, "message"=>"Error in line:".$line_id.", row:".($row+2).", column:".$col_arr[4]." (unit price ".$unit_price." is not numeric value)");
					} else {
						$data[$row]['unit_price'] = empty($unit_price)? "0":$unit_price;
					}
				}
			}
			$res['data'] = $data;
			// print_r($result);
			return $res;
		} catch (Exception $e) {
			return $res;
		}
	}

	function insertPriceByLineId($line_id="", $year="", $month="", $data=""){
		if( !empty($line_id) && !empty($year) && !empty($month) && !empty($data) ){
			ImportPrice::where('line_id', $line_id)->where('year', $year)->where('month', $month)->delete();
			foreach($data as $key=>$val){
				$import = new ImportPrice;
				$import->year = $year;
				$import->month = $month;
				$import->line_id = $line_id;
				$import->product_id = $val['product_id'];
				$import->process_id = $val['process_id'];
				$import->circle_time = $val['cycle_time'];
				$import->unit_price = $val['unit_price'];
				$import->save();
				// print_r($import->toArray());
			}
		}
	}

	public function importTarget(){
		$id = 'import-target';
		$url = url('admin/import-target-table');
		$headers = array('ID', 'Year', 'Month', 'Day', 'Line (Title)', 'Model (Title)', 'Processes (Number)', 'Target PC', 'Stock PC');
		$filters = array(
						"1" => "<input type=\"text\" class=\"column_filter\" data-column=\"1\">",
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">",
						"4" => "<input type=\"text\" class=\"column_filter\" data-column=\"4\">",
						"5" => "<input type=\"text\" class=\"column_filter\" data-column=\"5\">",
						"6" => "<input type=\"text\" class=\"column_filter\" data-column=\"6\">",
						"7" => "<input type=\"text\" class=\"column_filter\" data-column=\"7\">",
						"8" => "<input type=\"text\" class=\"column_filter\" data-column=\"8\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();
		return View::make('admins.reports.target', compact('datatable'));
	}

	public function getDataTarget(){
		$offset = Input::get('start', 0);
		$limit = Input::get('length', 10);
		$columns = Input::get('columns');
		$query = $this->target->skip($offset)->take($limit);
		$cols = array('id', 'year', 'month', 'day', 'line', 'product', 'process', 'target_pc', 'stock_pc');
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
			$query->where('year', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[2]['search']['value']) ){//title
			$search = $columns[2]['search']['value'];
			$query->where('month', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//title
			$search = $columns[3]['search']['value'];
			$query->where('day', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[4]['search']['value']) ){//line
			$search = $columns[4]['search']['value'];
			$query->whereHas('line', function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%");
			});
		}
		if( !empty($columns[5]['search']['value']) ){//model
			$search = $columns[5]['search']['value'];
			$query->whereHas('product', function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%");
			});
		}
		if( !empty($columns[6]['search']['value']) ){//model
			$search = $columns[6]['search']['value'];
			$query->whereHas('process', function ($query) use ($search) {
				$query->where('number', 'like', "%{$search}%");
			});
		}
		if( !empty($columns[7]['search']['value']) ){//title
			$search = $columns[7]['search']['value'];
			$query->where('target_pc', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[8]['search']['value']) ){//title
			$search = $columns[8]['search']['value'];
			$query->where('stock_pc', 'LIKE', "%{$search}%");
		}
		// echo $query->toSql();
		$count_query = clone $query;
		$import_target = $query->get();
		$items = array();
		foreach ($import_target as $key => & $obj) {
			$items[$key] = $obj->toArray();
			$items[$key]['line'] = $obj->line->title;
			$items[$key]['product'] = $obj->product->title;
			$items[$key]['process'] = $obj->process->number;
		}
		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $query->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);
		return Response::json($response);
	}

	function postImportTarget(){
		if (! Input::hasFile('file')){
			return Redirect::to("admin/import-target")->withErrors(array("Please upload a file"));
		}

		$file = Input::file('file');
		$year = Input::get('year');
		$month = Input::get('month');
		$storage_path = storage_path('imports');
		if (! is_dir($storage_path)) {
			mkdir($storage_path, 0755, TRUE);
		}

		$file->move($storage_path, $file->getClientOriginalName());
		$file_path = "$storage_path/{$file->getClientOriginalName()}";

		$lines = Line::all();
		$total_false = 0;
		foreach($lines as $line){
			$res[$line->id] = $this->validateTargetByLineId($file_path, $line->id);
			if($res[$line->id]['status']===FALSE){
				$total_false++;
				return Redirect::to("admin/import-target")->withErrors(array($res[$line->id]['message']));
			}
		}
		if($total_false < 1){
			foreach($lines as $line){
				if(!empty($res[$line->id]['data'])){
					$this->insertTargetByLineId($line->id, $year, $month, $res[$line->id]['data']);
				}
			}
			return Redirect::to("admin/import-target")->with('success', "Excel file of report is successfully imported.");
		}
	}

	function validateTargetByLineId($file_path="", $line_id=""){
		$res = array("status"=>TRUE, "message"=>"");
		$col_arr = array("0"=>"A", "1"=>"B", "2"=>"C", "3"=>"D", "4"=>"E", "5"=>"F", "6"=>"G", "7"=>"H", "8"=>"I", "9"=>"J", "10"=>"K", "11"=>"L", "12"=>"M", "13"=>"N", "14"=>"O", "15"=>"P", "16"=>"Q", "17"=>"R", "18"=>"S");
		try {
			$data = array();
			$result = Excel::selectSheets("target_pc_line".$line_id)->load($file_path, function($reader){
				$reader->ignoreEmpty();
				$reader->noHeading();
			})->get();
			foreach($result as $row=>$col){
				if( !empty($col[0]) && !empty($col[1]) && !empty($col[2]) ){
					$process_number = trim($col[0]);
					$process_name = trim($col[1]);
					$date = trim($col[2]);

					for($i=1; $i<=5; $i++){
						$modelIndex = (3*$i);
						$targetIndex = (3*$i)+1;
						$stockIndex = (3*$i)+2;
						if(!empty($col[$modelIndex]) && isset($col[$targetIndex]) && isset($col[$stockIndex])){
							$model[$i] = trim($col[$modelIndex]);
							$target_pc[$i] = trim($col[$targetIndex]);
							$stock_pc[$i] = trim($col[$stockIndex]);
							if(!empty($model[$i]) && (isset($target_pc[$i]) || isset($stock_pc[$i])) ){
								$data[$row][$i]['date'] = $date;
								$checkProcess = Process::where('number', $process_number)->first(array('id'));
								if( empty($checkProcess) && $res['status']===TRUE ){
									$res = array("status"=>FALSE, "message"=>"Error in line:".$line_id.", row:".($row+2).", column:".$col_arr[0]." (Not found process number:".$process_number." in database)");
								} else {
									$data[$row][$i]['process_id'] = $checkProcess->id;
								}
								$checkProduct = Product::where('title', $model[$i])->first(array('id'));
								if( empty($checkProduct) && $res['status']===TRUE ){
									$res = array("status"=>FALSE, "message"=>"Error in line:".$line_id.", row:".($row+2).", column:".$col_arr[$modelIndex]." (Not found model title:".$model[$i]." in database)");
								} else {
									$data[$row][$i]['product_id'] = $checkProduct->id;
								}
								if(!empty($target_pc[$i]) && !is_numeric($target_pc[$i]) && $res['status']===TRUE){
									$res = array("status"=>FALSE, "message"=>"Error in line:".$line_id.", row:".($row+2).", column:".$col[$targetIndex]." (target_pc is not numeric value)");
								} else {
									$data[$row][$i]['target_pc'] = empty($target_pc[$i])? "0":$target_pc[$i];
								}
								if(!empty($stock_pc[$i]) && !is_numeric($stock_pc[$i]) && $res['status']===TRUE){
									$res = array("status"=>FALSE, "message"=>"Error in line:".$line_id.", row:".($row+2).", column:".$col[$stockIndex]." (stock_pc is not numeric value)");
								} else {
									$data[$row][$i]['stock_pc'] = empty($stock_pc[$i])? "0":$stock_pc[$i];
								}
							}
						}
					}
				}
			}
			$res['data'] = $data;
			// print_r($result);
			return $res;
		} catch (Exception $e) {
			// echo $e->getMessage();
			return $res;
		}
	}

	function insertTargetByLineId($line_id="", $year="", $month="", $data=""){
		if( !empty($line_id) && !empty($year) && !empty($month) && !empty($data) ){
			ImportTarget::where('line_id', $line_id)->where('year', $year)->where('month', $month)->delete();
			foreach($data as $krow=>$row){
				foreach($row as $kval=>$val){
					$import = new ImportTarget;
					$import->year = $year;
					$import->month = $month;
					$import->day = sprintf('%02d', $val['date']);
					$import->line_id = $line_id;
					$import->product_id = $val['product_id'];
					$import->process_id = $val['process_id'];
					$import->target_pc = $val['target_pc'];
					$import->stock_pc = $val['stock_pc'];
					$import->save();
					/*echo "row=".$krow." col=".$kval;
					echo "<pre>";print_r($import->toArray());echo "</pre>";*/
				}
			}
		}
	}

	public function reportDaily(){
		return View::make('admins.reports.daily');
	}
}
