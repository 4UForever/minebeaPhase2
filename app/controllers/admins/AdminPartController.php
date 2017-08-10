<?php
use Carbon\Carbon;

class AdminPartController extends AdminBaseController
{

	protected $part;
	protected $product;
  	protected $process;
  	protected $line;

	public function __construct(Part $part, Product $product, Process $process, Line $line) {
		$this->part = $part;
		$this->product = $product;
		$this->process = $process;
		$this->line = $line;
	}

	public function getIndex() {
		$headers = array('<input type="checkbox" id="checkAll">', 'ID', 'Number', 'Name', 'Model', 'Process', 'Created at', 'Updated at', '');
		$id = 'parts';
		$url = url('admin/part/data-table');
		$filters = array(
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">",
						"4" => "<input type=\"text\" class=\"column_filter\" data-column=\"4\">",
						"5" => "<input type=\"text\" class=\"column_filter\" data-column=\"5\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();

		return View::make('admins.parts.index', compact('datatable'));
	}

	public function getCreate() {
		$products =  $this->product->get();

		$product_id = $products->first()->id;
		$product = $this->product->with('lines')->where('id', $product_id)->get()->first();
		$lines_arr = array();
		foreach ($product->lines as $line) {
			array_push($lines_arr, $line->id);
		}
		$processes = $this->process->whereIn('line_id', $lines_arr)->get();
		//print_r($processes->toArray());
		$process_old = $processes->first()->id;
		//echo "old=".$process_old;
		$process_select = View::make('admins.parts.model_process_select', compact('processes', 'process_old'))->render();

		return View::make('admins.parts.create', compact('products', 'process_select'));
	}

	public function postCreate() {
		$this->part->product_id = Input::get('product_id');
		$this->part->process_id = Input::get('process_id');
		$this->part->name = Input::get('name');
		$this->part->number = Input::get('number');

		if (!$this->part->save()) {
			$errors = $this->part->errors()->all();
			return Redirect::to('admin/part/create')->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/part")->with('success', "A part <i>{$this->part->name}</i> is successfully created");
	}

	public function getUpdate($id) {
		$part = $this->part->find($id);

		if (empty($part->id)) {
			return Redirect::to('admin/part')->withErrors(array("A part id $id could not be found"));
		}

		$products =  $this->product->get();

		$product_id = $part->product_id;
		$product = $this->product->with('lines')->where('id', $product_id)->get()->first();
		$lines_arr = array();
		foreach ($product->lines as $line) {
			array_push($lines_arr, $line->id);
		}
		$processes = $this->process->whereIn('line_id', $lines_arr)->get();
		$process_old = $part->process_id;
		$process_select = View::make('admins.parts.model_process_select', compact('processes', 'process_old'))->render();

		return View::make('admins.parts.update', compact('part', 'products', 'process_select'));
	}

	public function postUpdate($id) {
		$this->part = $this->part->find($id);

		if (empty($this->part->id)) {
			return Redirect::to('admin/part')->withErrors(array("A part id $id could not be found"));
		}
		$this->part->product_id = Input::get('product_id');
		$this->part->process_id = Input::get('process_id');
		$this->part->name = Input::get('name');
		$this->part->number = Input::get('number');

		if (!$this->part->save()) {
			$errors = $this->part->errors()->all();
			return Redirect::to("admin/part/$id/update")->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/part")->with('success', "A part <i>{$this->part->name}</i> is successfully updated");
	}

	public function getDelete($id) {
		$error = array();

		$part = $this->part->find($id);

		if (empty($part->id)) {
			$errors = array("A part id $id cannot be found");
			return Redirect::to('admin/part')->withErrors($errors);
		}

		return View::make('admins.parts.delete', compact('part'));
	}

	public function postDelete($id) {
		$error = array();

		$part = $this->part->find($id);

		if (empty($part->id)) {
			$errors = array("A part id $id cannot be found");
			return Redirect::to('admin/part')->withErrors($errors);
		}

		$iqc_lots = IqcLot::where('part_id', $part->id)->delete();
		if (!$part->delete()) {
			$errors = $part->errors()->all();
			return Redirect::to('admin/part')->withErrors($errors);
		}

		return Redirect::to("admin/part")->with('success', "A part <i>{$part->name}</i> is successfully deleted");
	}

	public function getDataTable() {
		$offset = Input::get('start');
		$limit = Input::get('length');
		$columns = Input::get('columns');

		$query = $this->part->with('product', 'process')->skip($offset)->take($limit);

		$cols = array('id', 'number', 'name', 'product', 'process', 'created_at', 'updated_at',);

		$orders = Input::get('order');

		foreach ($orders as $order) {
			$col_index = $order['column'];
			$query->orderBy($cols[$col_index], $order['dir']);
		}

		$search = Input::get('search');

		/*if (!empty($search['value'])) {
			$query->where('name', 'LIKE', "%{$search['value']}%");
		}*/
		if( !empty($columns[2]['search']['value']) ){//number
			$search = $columns[2]['search']['value'];
			$query->where('number', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//name
			$search = $columns[3]['search']['value'];
			$query->where('name', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[4]['search']['value']) ){//product
			$search = $columns[4]['search']['value'];
			$query->whereHas('product', function ($query) use ($search) {
				$query->where('title', 'like', "%{$search}%");
			});
		}
		if( !empty($columns[5]['search']['value']) ){//process
			$search = $columns[5]['search']['value'];
			$query->whereHas('process', function ($query) use ($search) {
				$query->where('number', 'like', "%{$search}%");
			});
		}

		$count_query = clone $query;

		$parts = $query->get();

		$items = array();
		foreach ($parts as $key => & $part) {
			$items[$key] = $part->toArray();
			$items[$key]['product'] = $part->product->title;
			$items[$key]['process'] = $part->process->number;
			$items[$key]['checkbox'] = "<input type=\"checkbox\" name=\"ids[]\" value=\"".$part->id."\">";

			$buttons = array(array('url' => url("admin/part/{$part->id}/update"), 'type' => 'warning', 'text' => 'Edit',), array('url' => url("admin/part/{$part->id}/delete"), 'type' => 'danger', 'text' => 'Delete',),);

			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}

		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $this->part->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);

		return Response::json($response);
	}

	public function getTest(){
		$query = $this->part->with('product', 'process')->skip(0)->take(10);
		$parts = $query->get();
		//dd($parts->toArray());
		echo "<br>";
		foreach ($parts as $key => & $part) {
			$items[$key] = $part->toArray();
			$items[$key]['product'] = $part->product->title;
			$items[$key]['process'] = $part->process->number;
		}
		print_r($items);
	}

	function getProcessSelect(){
		$product_id = Input::get('product_id');
		//select line_id from line_product WHERE product_id='$product_id'
		//select * from process where line_id in (query1)
		$product = $this->product->with('lines')->where('id', $product_id)->get()->first();
		$lines_arr = array();
		foreach ($product->lines as $line) {
			array_push($lines_arr, $line->id);
		}
		$processes = $this->process->whereIn('line_id', $lines_arr)->orderBy('id')->get();
		//print_r($processes->toArray());
		$process_old = isset($processes->first()->id) ? $processes->first()->id:"0";
		//echo $process_old;
		return View::make('admins.parts.model_process_select', compact('processes', 'process_old'));
	}

	function getImport(){
		return View::make('admins.parts.import');
	}

	function postImport(){
		if (! Input::hasFile('file')) {
			return Redirect::to("admin/part/import")->withErrors(array("Please upload a file"));
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
			$number = trim($col[0]);
			$name = trim($col[1]);
			$product = trim($col[2]);
			$process = trim($col[3]);
			$created_at = (empty($col[4])) ? date("Y-m-d H:i:s"):trim($col[4]);
			$updated_at = (empty($col[5])) ? date("Y-m-d H:i:s"):trim($col[5]);
			if( !empty($number) && !empty($name) && !empty($product) && !empty($process) ){
				//$part = Part::where('product_id', $product_id)->where('process_id', $process_id)->where('number', $number)->where('name', $name)->get()->count();
				$part = Part::where('number', $number)->where('name', $name)
					->whereHas('product', function ($query) use ($product) {
						$query->where('title', 'like', '%'.$product.'%');
					})
					->whereHas('process', function ($query) use ($process) {
						$query->where('number', 'like', '%'.$process.'%');
					})
				->count();
				//Check product, process exists
				$product_count = Product::where('title', $product)->get()->count();
				$process_count = Process::where('number', $process)->get()->count();
				if($product_count<1 || $process_count<1){
					$count_failure++;
				} else {
					if($part<1){
						$part_insert = new Part;
						$part_insert->number = $number;
						$part_insert->name = $name;
						$part_insert->product_id = Product::where('title', $product)->first()->id;
						$part_insert->process_id = Process::where('number', $process)->first()->id;
						$part_insert->created_at = $created_at;

						if( !empty($part_insert->number) && !empty($part_insert->name) && !empty($part_insert->product_id) && !empty($part_insert->process_id) ){
							$count_success++;
							$part_insert->save();
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
		return Redirect::to("admin/part")->with('success', "Excel file of Part data is successfully imported. <br>(Success : ".$count_success." rows, Failure : ".$count_failure." rows, Exist : ".$count_exist." rows)");
	}

	function getExport(){
		$parts = Part::with('product', 'process')->get();
		$data = array();
		foreach($parts as $key=>$row){
			$data[$key] = array(
				'number' => $row->number,
				'name' => $row->name,
				'model' => $row->product->title,
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
		Excel::create('Parts', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'Number', 'Name', 'Model', 'Process', 'Created at', 'Updated at'
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
		$delete['title'] = "Part";
		$delete['url'] = url("admin/part");
		$delete['warning'] = "Any IQC Lots in this part will be delete.";
		return View::make('admins.misc.delete_multi', compact('delete'));
	}

	function getDeleteMultiConfirmed()
	{
		$rows = Input::get('rows');
		$ids = explode(",", $rows);
		$affectedRows = Part::whereIn('id', $ids)->delete();
		$affectedIqc = IqcLot::whereIn('part_id', $ids)->delete();

		return Redirect::to("admin/part")->with('success', "Part ID : <i>{$rows}</i> was successfully deleted");
	}

	function partCheck(){
		$number = "7215-6370";
		$name = "Insulator 1";
		$product = "15VRX1003C18S";
		$process = "6-1-120-165";
		$created_at = date("Y-m-d H:i:s");
		$updated_at = "";

		//$part = Part::with('product')->first();
		//$part->product_title = $part->product->title;
		//$part = Product::find(7)->title;
		$part = Part::where('number', $number)->where('name', $name)//->with('product', 'process')
					->whereHas('product', function ($query) use ($product) {
						$query->where('title', 'like', '%'.$product.'%');
					})
					->whereHas('process', function ($query) use ($process) {
						$query->where('number', 'like', '%'.$process.'%');
					})
				->get();
		//$part->product_title = Product::find($part->product_id)->title;
		//$part->process_number = Process::find($part->process_id)->number;
		$part_insert = new Part;
		$part_insert->number = $number;
		$part_insert->name = $name;
		$part_insert->product_id = Product::where('title', $product)->first()->title;
		$part_insert->process_id = Process::where('number', $process)->first()->number;
		$part_insert->created_at = date("Y-m-d H:i:s");
		echo "<pre>";
		print_r($part_insert->toArray());
		echo "</pre>";
	}

	function testExport(){
		$users = User::select('id', 'email', 'qr_code', 'created_at')->skip(0)->take(10)->get();
		Excel::create('Users', function($excel) use($users) {
			$excel->sheet('Sheet 1', function($sheet) use($users) {
				$sheet->fromArray($users);
				$sheet->row(1, array(
					'ID', 'E-mail', 'QR Code', 'Created at'
				));
				$sheet->row(1, function($row) {
					$row->setFontWeight('bold');
				});
			});
		})->export('xls');
	}

}
