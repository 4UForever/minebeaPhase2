<?php

class AdminUtilityController extends AdminBaseController {
	public function index(){
		$date = date("Y-m-d", strtotime("-1 day"));
		return View::make('admins.reports.utility', compact('date'));
	}

	public function clearContinue(){
		$dbLotProcess = DB::table('lot_process')
						->whereRaw("process_log_id in ( SELECT process_log_from FROM process_log_continues WHERE process_log_to is null )")
						// ->offset(98)
						// ->limit(5)
						->get();
		/*echo "<pre>";
		print_r(json_decode(json_encode($dbLotProcess), true));
		echo "</pre>";*/
		$str = "";
		$arrLotProcess = json_decode(json_encode($dbLotProcess), true);
		if(count($arrLotProcess)>0){
			$str .= "Total continue process = ".count($arrLotProcess)."<br>";
			foreach ($arrLotProcess as $key => $value) {
				$lot_id = $value['lot_id'];
				$process_id = $value['process_id'];
				$sort = $value['sort'];
				$process_log_id = $value['process_log_id'];

				$dbLot = DB::table('lot_process')
						->where('lot_id', $lot_id)
						->where('process_id', $process_id)
						->where('sort', $sort)
						->where('process_log_id', '!=', $process_log_id)
						->get();
				$arrLot = json_decode(json_encode($dbLot), true);
				// echo "<pre>";print_r($arrLot);echo "</pre>";
				$str .= (count($arrLot)>0)? "":"Process from = ".$process_log_id." is not finish<br>";
				foreach ($arrLot as $kLot => $vLot) {
					$process_log_id2 = $vLot['process_log_id'];

					$user_id = DB::table('process_logs')->where('id', $process_log_id2)->first(['user_id'])->user_id;
					// print_r($user_id);
					$str .= "Process from = ".$process_log_id." to = ".$process_log_id2." user = ".$user_id."<br>";
					DB::table('process_log_continues')
					->where('process_log_from', $process_log_id)
					->where('process_log_to', NULL)
					->update([
						'process_log_to' => $process_log_id2,
						'working_user_id' => $user_id
					]);
				}
			}
		}
		$str .= "Clear finished";
		return $str;
	}

	public function getAjax(){
		$type = Input::get('type');
		$date = Input::get('date');
		if($type=="get-model"){
			$process_logs = ProcessLog::where('working_date', $date)->groupBy('product_id')->get(array('product_id', 'product_title'));
			foreach($process_logs as $key=>$val){
				echo '<option value="'.$val['product_id'].'">'.$val['product_title'].'</option>';
			}
		}
		if($type=="get-line"){
			$model_id = Input::get('model_id');
			$process_logs = ProcessLog::where('working_date', $date)->where('product_id', $model_id)->groupBy('line_id')->get(array('line_id', 'line_title'));
			foreach($process_logs as $key=>$val){
				echo '<option value="'.$val['line_id'].'">'.$val['line_title'].'</option>';
			}
		}
		if($type=="get-process"){
			$model_id = Input::get('model_id');
			$line_id = Input::get('line_id');
			$process_logs = ProcessLog::where('working_date', $date)->where('product_id', $model_id)->where('line_id', $line_id)->groupBy('process_id')->get(array('process_id', 'process_number', 'process_title'));
			foreach($process_logs as $key=>$val){
				echo '<option value="'.$val['process_id'].'">'.$val['process_title'].' ('.$val['process_number'].')</option>';
			}
		}
		if($type=="get-stkold"){
			$model_id = Input::get('model_id');
			$line_id = Input::get('line_id');
			$process_id = Input::get('process_id');
			$res = ImportTarget::where('line_id', $line_id)
					->where('product_id', $model_id)
					->where('process_id', $process_id)
					->where('year', substr($date, 0, 4))
					->where('month', substr($date, 5, 2))
					->where('day', substr($date, 8, 2))
					->get(['stock_pro'])->toArray();
			echo $res[0]['stock_pro'];
		}
		// return "test";
	}

	public function resetStockPro(){
		$date = Input::get('date');
		$model_id = Input::get('model_id');
		$line_id = Input::get('line_id');
		$process_id = Input::get('process_id');
		$stock_pro = Input::get('stock_pro');
		$error = 0;
		if(empty($date)){
			$error = 1;
			$msg = "Please select date";
			return Response::json(['error'=>$error, 'msg'=>$msg]);
		} else {
			if(strtotime($date) < strtotime("-30 days")){
				$error = 1;
				$msg = "Not allow to reset stock pro over 30 days";
				return Response::json(['error'=>$error, 'msg'=>$msg]);
			}
		}
		if(empty($model_id)){
			$error = 1;
			$msg = "Please select Model ID";
			return Response::json(['error'=>$error, 'msg'=>$msg]);
		}
		if(empty($line_id)){
			$error = 1;
			$msg = "Please select Line ID";
			return Response::json(['error'=>$error, 'msg'=>$msg]);
		}
		if(empty($process_id)){
			$error = 1;
			$msg = "Please select Process ID";
			return Response::json(['error'=>$error, 'msg'=>$msg]);
		}
		if(!is_numeric($stock_pro)){
			$error = 1;
			$msg = "Please input stock pro value in integer";
			return Response::json(['error'=>$error, 'msg'=>$msg]);
		}
		// return Response::json(['error'=>1, 'msg'=>'test error']);
		$res = ImportTarget::where('line_id', $line_id)
					->where('product_id', $model_id)
					->where('process_id', $process_id)
					->where('year', substr($date, 0, 4))
					->where('month', substr($date, 5, 2))
					->where('day', substr($date, 8, 2))
					->get()->count();
		if($res<1){
			$error = 1;
			$msg = "Not found current stock pro value, Please recheck your input.";
			return Response::json(['error'=>$error, 'msg'=>$msg]);
		} else {
			$error = 0;
			$msg = "";
			//set stock pro in that date
			$resUpdate = ImportTarget::where('line_id', $line_id)
					->where('product_id', $model_id)
					->where('process_id', $process_id)
					->where('year', substr($date, 0, 4))
					->where('month', substr($date, 5, 2))
					->where('day', substr($date, 8, 2))
					->update(array('stock_pro'=>$stock_pro));

			$startDate = date("Y-m-d", strtotime("+1 day", strtotime($date)));
			$lastDate = date("Y-m-d", strtotime("-1 day") );
			//find real last date if not yesterday in DB
			$resLastStkPro = ImportTarget::selectRaw('CONCAT(year, "-", month, "-", day) as stockDate, stock_pro, year, month, day')
					->whereRaw('CONCAT(year, "-", month, "-", day) between ? and ?', array($startDate, $lastDate))
					->where('line_id', $line_id)
					->where('product_id', $model_id)
					->where('process_id', $process_id)
					->orderBy('stockDate', 'desc')
					->first();
			if($resLastStkPro){
				$lastDate = $resLastStkPro['stockDate'];
			}
			//clear stock pro between that date+1 to yesterday
			$resUpdate = ImportTarget::selectRaw('CONCAT(year, "-", month, "-", day) as stockDate, stock_pro, year, month, day')
					->whereRaw('CONCAT(year, "-", month, "-", day) between ? and ?', array($startDate, $lastDate))
					->whereRaw('stock_pro is not NULL')
					->where('line_id', $line_id)
					->where('product_id', $model_id)
					->where('process_id', $process_id)
					->update(['stock_pro'=>NULL]);

			$setDate = date("Y-m-d", strtotime($date));
			$last_stock_pro = $stock_pro;
			while( strtotime($setDate) < strtotime($lastDate) ){
				$setDate = date("Y-m-d", strtotime("+1 day", strtotime($setDate)) );
				$stock_pro = $this->setStockPro($setDate, $line_id, $model_id, $process_id, $last_stock_pro);
				$msg .= "Date : ".$setDate." calculate new stock pro => ".$stock_pro."<br>";
				$last_stock_pro = $stock_pro;
			}
			$msg .= "Process successfully.";
			return Response::json(['error'=>$error, 'msg'=>$msg]);
		}
	}

	//***************--------copy from AdminReportController-------*****************
	function setStockPro($date, $line_id, $model_id, $process_id, $last_stock_pro){
		$process_logs = ProcessLog::where('working_date', $date)
						->where('line_id', $line_id)
						->where('product_id', $model_id)
						->orderBy('working_date', 'process_id')
						->get()
						->toArray();
		$output = $this->getOutput($process_logs, $date, $process_id);
		//find input next process
		$processDate = $this->getProcessDate($process_logs, $date);
		$nextProcessKey = array_search($process_id, array_column($processDate, 'process_id'))+1;
		if(count($processDate)<1){
			$stock_pro = $last_stock_pro;
		} else if($nextProcessKey < (count($processDate)-1)){
			$nextProcessId = $processDate[$nextProcessKey]['process_id'];
			$stock_pro = ($output + $last_stock_pro) - $this->getInput($process_logs, $date, $nextProcessId);
		} else {
			$stock_pro = 0;
		}
		$update = ImportTarget::where('line_id', $line_id)
						->where('product_id', $model_id)
						->where('process_id', $process_id)
						->where('year', substr($date, 0, 4))
						->where('month', substr($date, 5, 2))
						->where('day', substr($date, 8, 2))
						->whereNull('stock_pro')
						->update(array('stock_pro'=>$stock_pro));
		return $stock_pro;
	}

	function getProcessDate($process_logs, $date){
		$process_date = array();
		foreach($process_logs as $key=>$val){
			if($val['working_date']==$date){
				$arrCol = array_column($process_date, 'process_id');
				if(!in_array($val['process_id'], $arrCol)){
					array_push($process_date, array(
									'process_id' => $val['process_id'],
									'process_number' => $val['process_number'],
									'process_title' => $val['process_title'],
									'working_date' => $val['working_date'],
									'process_sort' => substr($val['process_number'], 4, 7).substr($val['process_number'], 3, 1).substr($val['process_number'], 0, 3)
								)
					);
				}
			}
		}
		// echo "<pre>";print_r($process_date);echo "</pre>";
		usort($process_date, function ($a, $b) {
			return strcmp($a['process_sort'], $b['process_sort']);
		});
		// echo "<pre>";print_r($process_date);echo "</pre>";
		return $process_date;
	}

	function getOutput($process_logs, $date, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return (($item['working_date']==$date) &&
			($item['process_id']==$process_id));
		}));
		//fix output need to deduct by WIP previous process 24/03/2018
		$processDate = $this->getProcessDate($process_logs, $date);
		$prevProcessKey = array_search($process_id, array_column($processDate, 'process_id'))-1;
		if($prevProcessKey>=0){
			$prevProcessId = $processDate[$prevProcessKey]['process_id'];
			$prevWip = $this->getWip($process_logs, $date, $prevProcessId);
		} else {
			$prevWip = 0;
		}
		$res = array_sum(array_column($filter, 'ok_qty')) - $prevWip;
		//-- fixed
		return $res;
	}

	function getInput($process_logs, $date, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return (($item['working_date']==$date) &&
			($item['process_id']==$process_id));
		}));
		$sumOk = array_sum(array_column($filter, 'ok_qty'));
		$sumNg = array_sum(array_column($filter, 'ng_qty'));
		$res = $sumOk + $sumNg;
		return $res;
	}

	function getWip($process_logs, $date, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return (($item['working_date']==$date) && ($item['process_id']==$process_id));
		}));
		$sumWip = array_sum(array_column($filter, 'wip_qty'));
		return $sumWip;
	}
	//***************--------copy from AdminReportController-------*****************

}
