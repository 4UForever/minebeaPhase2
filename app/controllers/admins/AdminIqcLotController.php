<?php
use Carbon\Carbon;

class AdminIqcLotController extends AdminBaseController
{

	protected $iqc_lot;
	protected $part;

	public function __construct(IqcLot $iqc_lot, Part $part) {
		$this->iqc_lot = $iqc_lot;
		$this->part = $part;
	}

	public function getIndex() {
		$headers = array('<input type="checkbox" id="checkAll">', 'ID', 'Number', 'Supplier', 'Invoice No.', 'Quantity', 'Part', 'Created at', 'Updated at', '');
		$id = 'iqc-lot';
		$url = url('admin/iqc-lot/data-table');
		$filters = array(
						"2" => "<input type=\"text\" class=\"column_filter\" data-column=\"2\">",
						"3" => "<input type=\"text\" class=\"column_filter\" data-column=\"3\">",
						"4" => "<input type=\"text\" class=\"column_filter\" data-column=\"4\">",
						"6" => "<input type=\"text\" class=\"column_filter\" data-column=\"6\">"
					);
		$datatable = View::make('admins.misc.datatable', compact('id', 'url', 'headers', 'filters'))->render();

		return View::make('admins.iqc_lots.index', compact('datatable'));
	}

	public function getCreate() {
		$parts = $this->part->all();
		return View::make('admins.iqc_lots.create', compact('parts'));
	}

	public function postCreate() {
		$this->iqc_lot->part_id = Input::get('part_id');
		$this->iqc_lot->number = Input::get('number');
		$this->iqc_lot->supplier_name = Input::get('supplier_name');
		$this->iqc_lot->invoice_number = Input::get('invoice_number');
		$this->iqc_lot->quantity = Input::get('quantity');

		if (!$this->iqc_lot->save()) {
			$errors = $this->iqc_lot->errors()->all();
			return Redirect::to('admin/iqc-lot/create')->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/iqc-lot")->with('success', "An IQC Lot <i>{$this->iqc_lot->number}</i> is successfully created");
	}

	public function getUpdate($id) {
		$iqc_lot = $this->iqc_lot->find($id);

		if (empty($iqc_lot->id)) {
			return Redirect::to('admin/iqc-lot')->withErrors(array("An IQC Lot id $id could not be found"));
		}

		$parts = $this->part->all();
		return View::make('admins.iqc_lots.update', compact('iqc_lot', 'parts'));
	}

	public function postUpdate($id) {
		$this->iqc_lot = $this->iqc_lot->find($id);

		if (empty($this->iqc_lot->id)) {
			return Redirect::to('admin/iqc-lot')->withErrors(array("An IQC Lot id $id could not be found"));
		}

		$this->iqc_lot->part_id = Input::get('part_id');
		$this->iqc_lot->number = Input::get('number');
		$this->iqc_lot->supplier_name = Input::get('supplier_name');
		$this->iqc_lot->invoice_number = Input::get('invoice_number');
		$this->iqc_lot->quantity = Input::get('quantity');

		if (!$this->iqc_lot->save()) {
			$errors = $this->iqc_lot->errors()->all();
			return Redirect::to("admin/iqc-lot/$id/update")->withErrors($errors)->withInput();
		}

		return Redirect::to("admin/iqc-lot")->with('success', "An IQC Lot <i>{$this->iqc_lot->number}</i> is successfully updated");
	}

	public function getDelete($id) {
		$error = array();

		$iqc_lot = $this->iqc_lot->find($id);

		if (empty($iqc_lot->id)) {
			$errors = array("An IQC Lot id $id cannot be found");
			return Redirect::to('admin/iqc-lot')->withErrors($errors);
		}

		return View::make('admins.iqc_lots.delete', compact('iqc_lot'));
	}

	public function postDelete($id) {
		$error = array();

		$iqc_lot = $this->iqc_lot->find($id);

		if (empty($iqc_lot->id)) {
			$errors = array("An IQC Lot id $id cannot be found");
			return Redirect::to('admin/iqc-lot')->withErrors($errors);
		}

		if (!$iqc_lot->delete()) {
			$errors = $iqc_lot->errors()->all();
			return Redirect::to('admin/iqc-lot')->withErrors($errors);
		}

		return Redirect::to("admin/iqc-lot")->with('success', "An IQC Lot <i>{$iqc_lot->number}</i> is successfully deleted");
	}

	public function getDataTable() {
		$offset = Input::get('start', 0);
		$limit = Input::get('length', 10);
		$columns = Input::get('columns');

		$query = $this->iqc_lot->with('part')->skip($offset)->take($limit);

		$cols = array('id', 'number', 'supplier_name', 'invoice_number', 'quantity', 'part', 'created_at', 'updated_at',);

		$orders = Input::get('order');

		foreach ($orders as $order) {
			$col_index = $order['column'];
			$query->orderBy($cols[$col_index], $order['dir']);
		}

		$search = Input::get('search');

		/*if (!empty($search['value'])) {
			$query->where('number', 'LIKE', "%{$search['value']}%");
		}*/
		if( !empty($columns[2]['search']['value']) ){//number
			$search = $columns[2]['search']['value'];
			$query->where('number', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[3]['search']['value']) ){//supplier
			$search = $columns[3]['search']['value'];
			$query->where('supplier_name', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[4]['search']['value']) ){//invoice
			$search = $columns[4]['search']['value'];
			$query->where('invoice_number', 'LIKE', "%{$search}%");
		}
		if( !empty($columns[6]['search']['value']) ){//part
			$search = $columns[6]['search']['value'];
			$query->whereHas('part', function ($query) use ($search) {
				$query->where('number', 'like', "%{$search}%");
			});
		}

		$count_query = clone $query;

		$iqc_lots = $query->get();

		$items = array();
		foreach ($iqc_lots as $key => & $iqc_lot) {
			$items[$key] = $iqc_lot->toArray();

			$items[$key]['part'] = $iqc_lot->part->number;
			$items[$key]['checkbox'] = "<input type=\"checkbox\" name=\"ids[]\" value=\"".$iqc_lot->id."\">";

			$buttons = array(array('url' => url("admin/iqc-lot/{$iqc_lot->id}/update"), 'type' => 'warning', 'text' => 'Edit',), array('url' => url("admin/iqc-lot/{$iqc_lot->id}/delete"), 'type' => 'danger', 'text' => 'Delete',),);

			$items[$key]['operations'] = View::make('admins.misc.button_group', compact('buttons'))->render();
		}

		$response = array('draw' => (int)Input::get('draw'), 'recordsTotal' => $this->iqc_lot->count(), 'recordsFiltered' => $count_query->skip(0)->count(), 'data' => $items,);

		return Response::json($response);
	}

	function getImport(){
		return View::make('admins.iqc_lots.import');
	}

	function postImport(){
		if (! Input::hasFile('file')) {
			return Redirect::to("admin/iqc-lot/import")->withErrors(array("Please upload a file"));
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
			$supplier_name = trim($col[1]);
			$invoice_number = trim($col[2]);
			$quantity = trim($col[3]);
			$part = trim($col[4]);
			$created_at = (empty($col[5])) ? date("Y-m-d H:i:s"):trim($col[5]);
			$updated_at = (empty($col[6])) ? date("Y-m-d H:i:s"):trim($col[6]);
			if( !empty($number) && !empty($supplier_name) && !empty($invoice_number) && !empty($quantity) && !empty($part)){
				$iqc_lot = IqcLot::where('number', $number)
							->where('supplier_name', $supplier_name)
							->where('invoice_number', $invoice_number)
							->where('quantity', $quantity)
					->whereHas('part', function ($query) use ($part) {
						$query->where('number', 'like', '%'.$part.'%');
					})
				->count();
				//Check part id exist
				$part_count = Part::where('number', $part)->get()->count();
				if($part_count<1){
					$count_failure++;
				} else {
					if($iqc_lot<1){
						$iqc_insert = new IqcLot;
						$iqc_insert->number = $number;
						$iqc_insert->supplier_name = $supplier_name;
						$iqc_insert->invoice_number = $invoice_number;
						$iqc_insert->quantity = $quantity;
						$iqc_insert->part_id = Part::where('number', $part)->first()->id;
						$iqc_insert->created_at = $created_at;
						/*echo "<pre>";
						print_r($iqc_insert->toArray());
						echo "</pre>";*/
						if( !empty($iqc_insert->number) && !empty($iqc_insert->supplier_name) && !empty($iqc_insert->invoice_number) && !empty($iqc_insert->quantity) && !empty($iqc_insert->part_id) ){
							$count_success++;
							$iqc_insert->save();
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
		return Redirect::to("admin/iqc-lot")->with('success', "Excel file of IQC Lot is successfully imported. <br>(Success : ".$count_success." rows, Failure : ".$count_failure." rows, Exist : ".$count_exist." rows)");
	}

	function getExport(){
		$iqc_lots = IqcLot::with('part')->get();
		$data = array();
		foreach($iqc_lots as $key=>$row){
			$data[$key] = array(
				'number' => $row->number,
				'supplier_name' => $row->supplier_name,
				'invoice_number' => $row->invoice_number,
				'quantity' => $row->quantity,
				'part' => $row->part->number,
				'created_at' => $row->created_at->format('Y-m-d H:i:s'),
				'updated_at' => $row->updated_at->format('Y-m-d H:i:s')
			);
		}
		/*
		echo "<pre>";
		print_r($data);
		echo "</pre>";
		*/
		Excel::create('IqcLots', function($excel) use($data) {
			$excel->sheet('Sheet 1', function($sheet) use($data) {
				$sheet->fromArray($data);
				$sheet->row(1, array(
					'Number', 'Supplier', 'Invoice No.', 'Quantity', 'Part', 'Created at', 'Updated at'
				));
				$sheet->row(1, function($row) {
					$row->setFontWeight('bold');
				});
			});
		})->export('xls');
	}

	function getImportCustom(){
		return View::make('admins.iqc_lots.import_custom');
	}

	function postImportCustom(){
		if (! Input::hasFile('file')) {
			return Redirect::to("admin/iqc-lot/import-custom")->withErrors(array("Please upload a file"));
		}

		$file = Input::file('file');
		$storage_path = storage_path('imports');
		if (! is_dir($storage_path)) {
		  mkdir($storage_path, 0755, TRUE);
		}

		$file->move($storage_path, $file->getClientOriginalName());
		$file_path = "$storage_path/{$file->getClientOriginalName()}";
		$count_exist = 0;
		$count_success = 0;
		$count_failure = 0;
		try {
			$result = Excel::selectSheetsByIndex(0)->load($file_path, function($reader){
				$reader->ignoreEmpty();
				$reader->noHeading();
			})->get();

			$rows = $result->toArray();
			/*echo "<pre>";
			print_r($rows);
			echo "</pre>";*/
		} catch( Exception $e ){
			echo "Caught exception : <b>".$e->getMessage()."</b><br/>";
		}

		foreach($rows as $row=>$col){
			$number = trim($col[0]);
			$part_name = trim($col[1]);
			$part = trim($col[2]);
			$supplier_name = trim($col[3]);
			$invoice_number = trim($col[4]);
			$quantity = trim($col[5]);
			$created_at = (empty($col[6])) ? date("Y-m-d H:i:s"):trim($col[6]);
			$updated_at = (empty($col[7])) ? date("Y-m-d H:i:s"):trim($col[7]);
			if( !empty($number) && !empty($supplier_name) && !empty($invoice_number) && !empty($quantity) && !empty($part)){
				$iqc_lot = IqcLot::where('number', $number)
							->where('supplier_name', $supplier_name)
							->where('invoice_number', $invoice_number)
							->where('quantity', $quantity)
					->whereHas('part', function ($query) use ($part) {
						$query->where('number', 'like', '%'.$part.'%');
					})
				->count();
				//Check part id exist
				$part_count = Part::where('number', $part)->get()->count();
				if($part_count<1){
					$count_failure++;
				} else {
					if($iqc_lot<1){
						$iqc_insert = new IqcLot;
						$iqc_insert->number = $number;
						$iqc_insert->supplier_name = $supplier_name;
						$iqc_insert->invoice_number = $invoice_number;
						$iqc_insert->quantity = $quantity;
						$iqc_insert->part_id = Part::where('number', $part)->first()->id;
						$iqc_insert->created_at = $created_at;
						if( !empty($iqc_insert->number) && !empty($iqc_insert->supplier_name) && !empty($iqc_insert->invoice_number) && !empty($iqc_insert->quantity) && !empty($iqc_insert->part_id) ){
							$count_success++;
							$iqc_insert->save();
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
		return Redirect::to("admin/iqc-lot")->with('success', "Excel file of IQC Lot is successfully imported. <br>(Success : ".$count_success." rows, Failure : ".$count_failure." rows, Exist : ".$count_exist." rows)");
	}

	function getDeleteMulti()
	{
		$delete['rows'] = Input::get('rows');
		$delete['title'] = "IQC Lot";
		$delete['url'] = url("admin/iqc-lot");
		$delete['warning'] = "";
		return View::make('admins.misc.delete_multi', compact('delete'));
	}

	function getDeleteMultiConfirmed()
	{
		$rows = Input::get('rows');
		$ids = explode(",", $rows);
		$affectedIqc = IqcLot::whereIn('id', $ids)->delete();

		return Redirect::to("admin/iqc-lot")->with('success', "IQC-Lot ID : <i>{$rows}</i> was successfully deleted");
	}

}
