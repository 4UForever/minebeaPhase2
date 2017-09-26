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
		$headers = array('ID', 'Year', 'Month', 'Line (Title)', 'Model (Title)', 'Processes (Number)', 'Circle Time', 'Unit Price');
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
		// echo "<pre>";print_r($lines->toArray());echo "</pre>";
		foreach($lines as $line){
			// if($line->id=="1"){
				$this->importPriceByLineId($file_path, $line->id, $year, $month);
			// }
		}
		return Redirect::to("admin/import-price")->with('success', "Excel file of report is successfully imported.");
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
		// echo "<pre>";print_r($lines->toArray());echo "</pre>";
		foreach($lines as $line){
			// if($line->id=="1"){
				$this->importTargetByLineId($file_path, $line->id, $year, $month);
			// }
		}
		return Redirect::to("admin/import-target")->with('success', "Excel file of report is successfully imported.");
	}

	function importPriceByLineId($file_path="", $line_id="", $year="", $month=""){
		echo "load price sheet line".$line_id."<br>";
		try {
			ImportPrice::where('line_id', $line_id)->where('year', $year)->where('month', $month)->delete();
			$result = Excel::selectSheets("line".$line_id)->load($file_path, function($reader){
				$reader->ignoreEmpty();
				$reader->noHeading();
			})->get();
			foreach($result as $row=>$col){
				/*echo "row=".$row."<br>";
				echo "<pre>";print_r($col->toArray());echo "</pre>";*/
				$process_number = trim($col[0]);
				$process_name = trim($col[1]);
				$model = trim($col[2]);
				$circle_time = (empty($col[3]))? "0":trim($col[3]);
				$unit_price = (empty($col[4]))? "0":trim($col[4]);
				if(!empty($process_number) && ($model)){//ignore empty rows
					// echo "process_number=".$process_number."<br>";
					if(!Process::where('number', $process_number)->first(array('id'))->id){
						throw new Exception("Cannot find process number : ".$process_number." in database");
					}
					if(!Product::where('title', $model)->first(array('id'))->id){
						throw new Exception("Cannot find model title : ".$model." in database");
					}
					$process_id = Process::where('number', $process_number)->first(array('id'))->id;
					$model_id = Product::where('title', $model)->first(array('id'))->id;
					$import = new ImportPrice;
					$import->year = $year;
					$import->month = $month;
					$import->line_id = $line_id;
					$import->product_id = $model_id;
					$import->process_id = $process_id;
					$import->circle_time = $circle_time;
					$import->unit_price = $unit_price;
					$import->save();
					// echo "<pre>";print_r($import->toArray());echo "</pre>";
				}
			}
		} catch (Exception $e) {
			echo "Cannot import file<br />Message: ".$e->getMessage();
		}
	}

	function importTargetByLineId($file_path="", $line_id="", $year="", $month=""){
		echo "load target sheet line".$line_id."<br>";
		try {
			ImportTarget::where('line_id', $line_id)->where('year', $year)->where('month', $month)->delete();
			$result = Excel::selectSheets("target_pc_line".$line_id)->load($file_path, function($reader){
				$reader->ignoreEmpty();
				$reader->noHeading();
			})->get();
			foreach($result as $row=>$col){
				/*echo "row=".$row."<br>";
				echo "<pre>";print_r($col->toArray());echo "</pre>";*/
				$process_number = trim($col[0]);
				$process_name = trim($col[1]);
				$date = trim($col[2]);

				for($i=1; $i<=5; $i++){
					$modelIndex = (3*$i);
					$targetIndex = (3*$i)+1;
					$stockIndex = (3*$i)+2;
					if(!empty($col[$modelIndex]) && !empty($col[$targetIndex]) && !empty($col[$stockIndex])){
						$model[$i] = trim($col[$modelIndex]);
						$target_pc[$i] = trim($col[$targetIndex]);
						$stock_pc[$i] = trim($col[$stockIndex]);
						if(!empty($model[$i]) && !empty($target_pc[$i]) && !empty($stock_pc[$i])){
							if(!Process::where('number', $process_number)->first(array('id'))->id){
								throw new Exception("Cannot find process number : ".$process_number." in database");
							}
							if(!Product::where('title', $model[$i])->first(array('id'))->id){
								throw new Exception("Cannot find model title : ".$model[$i]." in database");
							}
							$process_id = Process::where('number', $process_number)->first(array('id'))->id;
							$model_id = Product::where('title', $model[$i])->first(array('id'))->id;
							$import = new ImportTarget;
							$import->year = $year;
							$import->month = $month;
							$import->day = sprintf('%02d', $date);
							$import->line_id = $line_id;
							$import->product_id = $model_id;
							$import->process_id = $process_id;
							$import->target_pc = $target_pc[$i];
							$import->stock_pc = $stock_pc[$i];
							$import->save();
							// echo "<pre>";print_r($import->toArray());echo "</pre>";
						}
					}
				}
			}
		} catch (Exception $e) {
			echo "Cannot import file<br />Message: ".$e->getMessage();
		}
	}

	public function reportDaily(){
		return View::make('admins.reports.daily');
	}
}
