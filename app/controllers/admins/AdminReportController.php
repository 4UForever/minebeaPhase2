<?php
use Carbon\Carbon;

class AdminReportController extends AdminBaseController
{
	public function reportDaily(){
		$date = Input::get('date');
		$line_id = Input::get('line_id');
		$model_id = Input::get('model_id');
		$type = Input::get('type');
		$models = array();
		$lines = array();
		$model_title = "";
		$line_title = "";
		$models = array();
		$lines = array();
		$data = array();
		$shiftsAll = array();
		$resShiftSize = 0;
		$total = array();
		if(!empty($date) && !empty($model_id) && !empty($line_id)){
			$models = ProcessLog::where('working_date', $date)->groupBy('product_id')->get(array('product_id', 'product_title'));
			$lines = ProcessLog::where('working_date', $date)->where('product_id', $model_id)->groupBy('line_id')->get(array('line_id', 'line_title'));

			$model_filter = $models->filter(function($item) use ($model_id){ return $item->product_id == $model_id; })->first();
			$model_title = $model_filter->product_title;
			$line_filter = $lines->filter(function($item) use ($line_id){ return $item->line_id == $line_id; })->first();
			$line_title = $line_filter->line_title;

			//query data
			$process_logs = $this->getAllProcessLog($date, $line_id, $model_id);
			$process_date = $this->getProcessDate($process_logs, $date);
			$timePriceAll = $this->getAllTimeNPrice($date, $line_id, $model_id);
			$targetAll = $this->getAllTargetNstock($date, $line_id, $model_id);
			$shiftsAll = $this->getAllShift();
			$resShiftSize = count($shiftsAll)*2;
			$allProcessBreakFlagDate = $this->getAllProcessBreakFlagDate($process_logs, $date);
			$allYieldAccumDate = $this->getYieldAccumDate($process_logs, $date, $process_date);
			$allAccumYieldAccumDate = $this->getAccumYieldAccumDate($process_logs, $process_date);
			foreach($process_date as $process_key=>$process_arr){
				// echo "process=".$process_arr['process_id']." number=".$process_arr['process_number']."<br>";
				// if($process_arr['process_id']=="9"){
					$process_id = $process_arr['process_id'];
					$timeNprice = $this->getTimeNprice($timePriceAll, $date, $line_id, $model_id, $process_id);
					$targetNstock = $this->getTargetNstock($targetAll, $date, $line_id, $model_id, $process_id);

					$data[$process_id]['process'][0] = $process_arr['process_title'];
					$data[$process_id]['accum'][0] = "Accum (".$process_arr['process_number'].")";
					$data[$process_id]['process'][1] = $timeNprice['cycle_time'];
					$data[$process_id]['process'][2] = 1;
					$data[$process_id]['accum'][2] = $this->getWorkingDay($process_logs, $date, $line_id, $model_id, $process_id);
					$data[$process_id]['process'][3] = $targetNstock['target_pc'];
					$data[$process_id]['accum'][3] = $this->getAccumTarget($targetAll, $date, $line_id, $model_id, $process_id);
					$data[$process_id]['process'][4] = $this->getInput($process_logs, $date, $process_id);
					$data[$process_id]['accum'][4] = $this->getAccumInput($process_logs, $process_id);
					$data[$process_id]['process'][5] = $this->getOutput($process_logs, $date, $process_id);
					$data[$process_id]['accum'][5] = $this->getAccumOutput($process_logs, $process_id);
					$data[$process_id]['process'][6] = $this->getYieldNg($process_logs, $date, $process_id);
					$data[$process_id]['accum'][6] = $this->getAccumYieldNg($process_logs, $process_id);
					$data[$process_id]['process'][7] = $allYieldAccumDate[$process_id];
					$data[$process_id]['accum'][7] = $allAccumYieldAccumDate[$process_id];
					$data[$process_id]['process'][8] = $data[$process_id]['process'][5] - $data[$process_id]['process'][3];//output - target pc
					$data[$process_id]['accum'][8] = $data[$process_id]['accum'][5] - $data[$process_id]['accum'][3];//accum output - accum target pc
					$data[$process_id]['process'][9] = $this->getWip($process_logs, $date, $process_id);
					$data[$process_id]['process'][10] = $this->getStockPro($process_logs, $date, $line_id, $model_id, $process_id);
					$data[$process_id]['process'][11] = $targetNstock['stock_pc'];
					$data[$process_id]['process'][12] = ($data[$process_id]['process'][10]+$data[$process_id]['process'][9])-$data[$process_id]['process'][11];//(stock_pro+wip)-stock_pc
					//result of shift Ng
					$shiftSum = $this->getShiftSum($process_logs, $shiftsAll, $date, $process_id);
					$colCurrent = 13;
					foreach($shiftsAll as $key=>$val){
						$data[$process_id]['process'][$colCurrent] = $shiftSum['sumShiftOk'][$val['id']];
						$data[$process_id]['process'][($colCurrent+1)] = $shiftSum['sumShiftNg'][$val['id']];
						$colCurrent = $colCurrent+2;
					}
					//end result shift
					$dateNgSum = $this->getDateNgSum($process_logs, $date, $process_id);
					$data[$process_id]['process'][$colCurrent] = $dateNgSum['sumNg1'];//col=28
					$data[$process_id]['process'][($colCurrent+1)] = $dateNgSum['sumNg2'];
					$data[$process_id]['process'][($colCurrent+2)] = $dateNgSum['sumSetup'];
					$data[$process_id]['process'][($colCurrent+3)] = $dateNgSum['sumDt'];
					$data[$process_id]['process'][($colCurrent+4)] = $dateNgSum['sumTtl'];
					$data[$process_id]['process'][($colCurrent+5)] = number_format(($timeNprice['unit_price']*$dateNgSum['sumTtl']), 2);
					$accumNgSum = $this->getAccumNgSum($process_logs, $process_id);
					$data[$process_id]['accum'][$colCurrent] = $accumNgSum['sumNg1'];//col=28
					$data[$process_id]['accum'][($colCurrent+1)] = $accumNgSum['sumNg2'];
					$data[$process_id]['accum'][($colCurrent+2)] = $accumNgSum['sumSetup'];
					$data[$process_id]['accum'][($colCurrent+3)] = $accumNgSum['sumDt'];
					$data[$process_id]['accum'][($colCurrent+4)] = $accumNgSum['sumTtl'];
					$data[$process_id]['accum'][($colCurrent+5)] = number_format(($timeNprice['unit_price']*$accumNgSum['sumTtl']), 2);
					$remarkNdowntime = $this->getDownTimeSum($process_logs, $allProcessBreakFlagDate, $date, $process_id);
					$data[$process_id]['process'][($colCurrent+6)] = $remarkNdowntime['remark'];
					$data[$process_id]['process'][($colCurrent+7)] = $remarkNdowntime['downtime'];
					$data[$process_id]['process'][($colCurrent+8)] = $timeNprice['unit_price'];

					for($i=0; $i<=($colCurrent+8); $i++){
						if(!isset($data[$process_id]['accum'][$i])) $data[$process_id]['accum'][$i] = "";
					}
				// }
			}
			$total['cycle_time'] = number_format(array_sum(array_column(array_column($data, 'process'), 1)), 2);
			$total['man_power'] = "xx";
			$total['prod_100'] = (!empty($total['cycle_time']))? number_format(((3600*7)/$total['cycle_time']), 2):"0.00";
			$total['prod_80'] = number_format(($total['prod_100']*0.8), 2);
			$total['day_ttl'] = array_sum(array_column(array_column($data, 'process'), ($colCurrent+4)));
			$total['day_price'] = array_sum(array_column(array_column($data, 'process'), ($colCurrent+5)));
			$total['accum_ttl'] = array_sum(array_column(array_column($data, 'accum'), ($colCurrent+4)));
			$total['accum_price'] = array_sum(array_column(array_column($data, 'accum'), ($colCurrent+5)));
		}
		// echo "<pre>";print_r($data);echo "</pre>";
		//make function get all import price and get all import target
		if($type=="excel"){
			$this->exportExcel($data, $shiftsAll, $resShiftSize, $total);
			// return View::make('admins.reports.daily-excel', compact('date', 'model_id', 'line_id', 'data', 'shiftsAll', 'resShiftSize', 'total'));
		} else {
			return View::make('admins.reports.daily', compact('date', 'model_id', 'line_id', 'model_title', 'line_title', 'models', 'lines', 'data', 'shiftsAll', 'resShiftSize', 'total'));
		}
	}

	public function reportDaily1(){//for test
		$date = "2018-02-09";
		$model_id = "7";
		$line_id = "6";
		$process_id = "9";
		// $this->exportExcel();
		$process_logs = $this->getAllProcessLog($date, $line_id, $model_id);
		$processDate = $this->getProcessDate($process_logs, $date);
		echo "<pre>";print_r($processDate);echo "</pre>";
		$prevProcessKey = array_search($process_id, array_column($processDate, 'process_id'))-1;
		if($prevProcessKey>=0){
			$prevProcessId = $processDate[$prevProcessKey]['process_id'];
			$prevWip = $this->getWip($process_logs, $date, $prevProcessId);
		} else {
			$prevWip = 0;
		}
		/*$filter = array_filter($process_date, function($item) use ($process_id){
			return ($item['process_id']==$process_id);
		});*/
		echo "<pre>";print_r($prevWip);echo "</pre>";
		/*foreach($process_date as $process_key=>$process_arr){
			if($process_key==8){
			$stock_pro = $this->getStockPro($process_logs, $date, $line_id, $model_id, $process_arr['process_id']);
			echo "id=".$process_arr['process_id']." pro=".$stock_pro."<br>";
			}
		}*/
		//SELECT * FROM `process_log_breaks` WHERE process_log_id in (SELECT id FROM `process_logs` WHERE working_date='2017-10-06' and line_id='6' and product_id='7') and break_id in (SELECT id FROM break_reasons WHERE flag='1')
		echo "finish";
	}

	function getStockPro($process_logs, $date, $line_id, $model_id, $process_id){
		$output = $this->getOutput($process_logs, $date, $process_id);
		$last_stock_pro = $this->getLastStockPro($date, $line_id, $model_id, $process_id);
		//find input next process
		$processDate = $this->getProcessDate($process_logs, $date);
		$nextProcessKey = array_search($process_id, array_column($processDate, 'process_id'))+1;
		if($nextProcessKey < (count($processDate)-1) ){
			$nextProcessId = $processDate[$nextProcessKey]['process_id'];
			$stock_pro = ($output + $last_stock_pro) - $this->getInput($process_logs, $date, $nextProcessId);
		} else {
			$stock_pro = 0;
		}
		return $stock_pro;
	}

	function getLastStockPro($date, $line_id, $model_id, $process_id){
		$lastDate = date("Y-m-d", strtotime("-1 day", strtotime($date)) );
		$select = ImportTarget::where('line_id', $line_id)
						->where('product_id', $model_id)
						->where('process_id', $process_id)
						->where('year', substr($lastDate, 0, 4))
						->where('month', substr($lastDate, 5, 2))
						->where('day', substr($lastDate, 8, 2))
						->first();
		if(!$select){
			$select = new ImportTarget;
			$select->year = substr($lastDate, 0, 4);
			$select->month = substr($lastDate, 5, 2);
			$select->day = substr($lastDate, 8, 2);
			$select->line_id = $line_id;
			$select->product_id = $model_id;
			$select->process_id = $process_id;
			$select->stock_pro === NULL;
			$select->save();
		}
		if($select->stock_pro === NULL){
			$startDate = date("Y-m-d", strtotime("-1 month", strtotime($date)) );
			$res = ImportTarget::selectRaw('CONCAT(year, "-", month, "-", day) as stockDate, stock_pro, year, month, day')
							->whereRaw('CONCAT(year, "-", month, "-", day) between ? and ?', array($startDate, $date))
							->whereRaw('stock_pro is not NULL')
							->where('line_id', $line_id)
							->where('product_id', $model_id)
							->where('process_id', $process_id)
							->orderBy('stockDate', 'desc')
							->first();
			if(!$res){
				$select->stock_pro = 0;
				$select->save();
				return $select->stock_pro;
			} else {
				$setDate = date("Y-m-d", strtotime($res->stockDate));
				while( strtotime($setDate) < strtotime($lastDate) ){
					$setDate = date("Y-m-d", strtotime("+1 day", strtotime($setDate)) );
					$stock_pro = $this->setStockPro($setDate, $line_id, $model_id, $process_id, $res->stock_pro);
				}
				return $stock_pro;
			}
		} else {
			return $select->stock_pro;
		}
	}

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
		if($nextProcessKey < (count($processDate)-1) ){
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
						->update(array('stock_pro'=>$stock_pro));
		return $stock_pro;
	}

	function getAllProcessLog($date, $line_id, $model_id){
		$res = ProcessLog::whereRaw('(SUBSTRING(working_date, 1, 7)=?)', array(substr($date, 0, 7)))
						->where('working_date', '<=', $date)
						->where('line_id', $line_id)
						->where('product_id', $model_id)
						->orderBy('working_date', 'process_id')
						->get()
						->toArray();
		return $res;
	}

	function getAllTargetNstock($date, $line_id, $model_id){
		$res = ImportTarget::whereRaw('CONCAT(year, "-", month, "-", day) <= ?', array($date))
						->where('year', substr($date, 0, 4))
						->where('month', substr($date, 5, 2))
						->where('line_id', $line_id)
						->where('product_id', $model_id)
						->orderBy('day', 'process_id')
						->get()
						->toArray();
		return $res;
	}

	function getAllTimeNPrice($date, $line_id, $model_id){
		$res = ImportPrice::where('year', substr($date, 0, 4))
						->where('month', substr($date, 5, 2))
						->where('line_id', $line_id)
						->where('product_id', $model_id)
						->get()
						->toArray();
		return $res;
	}

	function getAllShift(){
		return ShiftCode::all()->toArray();
	}

	function getAllProcessBreakFlagDate($process_logs, $date){
		$breakFlagAll = BreakReason::where('flag', '1')->get()->toArray();
		$allProcessDate = array_values(array_filter($process_logs, function($item) use ($date){
			return ($item['working_date']==$date);
		}));
		$allProcessDateIds = array_column($allProcessDate, 'id');
		$breakAllIds = array_column($breakFlagAll, 'id');
		$allProcessBreakFlagDate = ProcessLogBreak::whereIn('process_log_id', $allProcessDateIds)->whereIn('break_id', $breakAllIds)->get()->toArray();
		return $allProcessBreakFlagDate;
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

	function getTimeNprice($timePriceAll, $date, $line_id, $model_id, $process_id){
		$filter = array_filter($timePriceAll, function($item) use ($date, $line_id, $model_id, $process_id){
			return (($item['year']."-".$item['month']==substr($date, 0, 7)) &&
			($item['line_id']==$line_id) &&
			($item['product_id']==$model_id) &&
			($item['process_id']==$process_id));
		});
		if(!empty($filter)){
			$timeNprice = array_values($filter)[0];
		} else {
			$timeNprice['circle_time'] = 0;
			$timeNprice['unit_price'] = 0;
		}
		return array("cycle_time"=>$timeNprice['circle_time'], "unit_price"=>$timeNprice['unit_price']);
	}

	function getTargetNstock($targetAll, $date, $line_id, $model_id, $process_id){
		$filter = array_filter($targetAll, function($item) use ($date, $line_id, $model_id, $process_id){
			return (($item['year']."-".$item['month']."-".$item['day']==$date) &&
			($item['line_id']==$line_id) &&
			($item['product_id']==$model_id) &&
			($item['process_id']==$process_id));
		});
		if(!empty($filter)){
			$targetNstock = array_values($filter)[0];
		} else {
			$targetNstock['target_pc'] = 0;
			$targetNstock['stock_pc'] = 0;
		}
		return array("target_pc"=>$targetNstock['target_pc'], "stock_pc"=>$targetNstock['stock_pc']);
	}

	function getWorkingDay($process_logs, $date, $line_id, $model_id, $process_id){
		$working_date = array();
		foreach($process_logs as $key=>$val){
			if( ($val['working_date']==$date) && ($val['line_id']==$line_id) && ($val['product_id']==$model_id) && ($val['process_id']==$process_id) ){
				if(!in_array($val['working_date'], $working_date)){
					array_push($working_date, $val['working_date']);
				}
			}
		}
		return count($working_date);
	}

	function getAccumTarget($targetAll, $date, $line_id, $model_id, $process_id){
		$filter = array_values(array_filter($targetAll, function($item) use ($date, $line_id, $model_id, $process_id){
			return (($item['year']."-".$item['month']==substr($date, 0, 7)) &&
			($item['line_id']==$line_id) &&
			($item['product_id']==$model_id) &&
			($item['process_id']==$process_id));
		}));
		$sum = array_sum(array_column($filter, 'target_pc'));
		return $sum;
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

	function getAccumInput($process_logs, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($process_id){
			return ($item['process_id']==$process_id);
		}));
		$sumOk = array_sum(array_column($filter, 'ok_qty'));
		$sumNg = array_sum(array_column($filter, 'ng_qty'));
		$res = $sumOk + $sumNg;
		return $res;
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

	function getAccumOutput($process_logs, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($process_id){
			return ($item['process_id']==$process_id);
		}));
		$res = array_sum(array_column($filter, 'ok_qty'));
		return $res;
	}

	function getYieldNg($process_logs, $date, $process_id){
		/*$filter = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return (($item['working_date']==$date) &&
			($item['process_id']==$process_id));
		}));
		$sumOk = array_sum(array_column($filter, 'ok_qty'));
		$sumNg = array_sum(array_column($filter, 'ng_qty'));
		$res = (($sumOk+$sumNg)>0)? (($sumNg / ($sumOk+$sumNg))*100):100;
		return number_format($res, 2);*/
		$output = $this->getOutput($process_logs, $date, $process_id);
		$dateNgSum = $this->getDateNgSum($process_logs, $date, $process_id);
		if( ($dateNgSum['sumNg2']+$dateNgSum['sumSetup']+$output)>0 ){
			$res = (1-(($dateNgSum['sumNg2']+$dateNgSum['sumSetup'])/($dateNgSum['sumNg2']+$dateNgSum['sumSetup']+$output)))*100;
		} else {
			$res = 100;
		}
		return number_format($res, 2);
	}

	function getAccumYieldNg($process_logs, $process_id){
		$output = $this->getAccumOutput($process_logs, $process_id);
		$dateNgSum = $this->getAccumNgSum($process_logs, $process_id);
		if( ($dateNgSum['sumNg2']+$dateNgSum['sumSetup']+$output)>0 ){
			$res = (1-(($dateNgSum['sumNg2']+$dateNgSum['sumSetup'])/($dateNgSum['sumNg2']+$dateNgSum['sumSetup']+$output)))*100;
		} else {
			$res = 100;
		}
		return number_format($res, 2);
	}

	function getYieldAccumDate($process_logs, $date, $process_date){//get yield accum all process in date
		$yieldAccum = array();
		$lastYieldAccum = "";
		foreach($process_date as $key=>$val){
			$output = $this->getOutput($process_logs, $date, $val['process_id']);
			if($key==0){
				$yieldAccum[$val['process_id']] = $this->getYieldNg($process_logs, $date, $val['process_id']);
			} else {
				if($output==0){
					$yieldAccum[$val['process_id']] = $lastYieldAccum;
				} else {
					$yieldNg = $this->getYieldNg($process_logs, $date, $val['process_id']);
					if(empty($lastYieldAccum)){
						$yieldAccum[$val['process_id']] = $yieldNg;
					} else {
						$yieldAccum[$val['process_id']] = number_format((($yieldNg*$lastYieldAccum)/100), 2);
					}
				}
			}
			$lastYieldAccum = $yieldAccum[$val['process_id']];
		}
		return $yieldAccum;
	}

	function getAccumYieldAccumDate($process_logs, $process_date){//get accum yield accum all process in date
		$yieldAccum = array();
		$lastYieldAccum = "";
		foreach($process_date as $key=>$val){
			$output = $this->getAccumOutput($process_logs, $val['process_id']);
			if($key==0){
				$yieldAccum[$val['process_id']] = $this->getAccumYieldNg($process_logs, $val['process_id']);
			} else {
				if($output==0){
					$yieldAccum[$val['process_id']] = $lastYieldAccum;
				} else {
					$yieldNg = $this->getAccumYieldNg($process_logs, $val['process_id']);
					if(empty($lastYieldAccum)){
						$yieldAccum[$val['process_id']] = $yieldNg;
					} else {
						$yieldAccum[$val['process_id']] = number_format((($yieldNg*$lastYieldAccum)/100), 2);
					}
				}
			}
			$lastYieldAccum = $yieldAccum[$val['process_id']];
		}
		return $yieldAccum;
	}

	function getWip($process_logs, $date, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return (($item['working_date']==$date) && ($item['process_id']==$process_id));
		}));
		$sumWip = array_sum(array_column($filter, 'wip_qty'));
		return $sumWip;
	}

	function getShiftSum($process_logs, $shifts, $date, $process_id){
		$filter = array();
		$sumShiftNg = array();
		$sumShiftOk = array();
		foreach($shifts as $key=>$val){
			$shift_id = $val['id'];
			$filter[$shift_id] = array_values(array_filter($process_logs, function($item) use ($date, $process_id, $shift_id){
				return (($item['working_date']==$date) &&
				($item['process_id']==$process_id) &&
				($item['shift_id']==$shift_id));
			}));
			$sumShiftNg[$shift_id] = array_sum(array_column($filter[$shift_id], 'ng1_qty'));
			$sumShiftOk[$shift_id] = array_sum(array_column($filter[$shift_id], 'ok_qty'));
		}
		return array("sumShiftNg"=>$sumShiftNg, "sumShiftOk"=>$sumShiftOk);
	}

	function getDateNgSum($process_logs, $date, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return (($item['working_date']==$date) &&
			($item['process_id']==$process_id));
		}));
		$sumNg1 = array_sum(array_column($filter, 'ng1_qty'));
		$sumNg2 = array_sum(array_column($filter, 'ng_qty'));
		$sumSetup = array_sum(array_column($filter, 'setup'));
		$sumDt = array_sum(array_column($filter, 'dt'));
		$sumTtl = $sumNg2+$sumSetup+$sumDt;
		return array("sumNg1"=>$sumNg1, "sumNg2"=>$sumNg2, "sumSetup"=>$sumSetup, "sumDt"=>$sumDt, "sumTtl"=>$sumTtl);
	}

	function getAccumNgSum($process_logs, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($process_id){
			return ($item['process_id']==$process_id);
		}));
		$sumNg1 = array_sum(array_column($filter, 'ng1_qty'));
		$sumNg2 = array_sum(array_column($filter, 'ng_qty'));
		$sumSetup = array_sum(array_column($filter, 'setup'));
		$sumDt = array_sum(array_column($filter, 'dt'));
		$sumTtl = $sumNg2+$sumSetup+$sumDt;
		return array("sumNg1"=>$sumNg1, "sumNg2"=>$sumNg2, "sumSetup"=>$sumSetup, "sumDt"=>$sumDt, "sumTtl"=>$sumTtl);
	}

	function getDownTimeSum($process_logs, $allProcessBreakFlagDate, $date, $process_id){
		$onlyProcessDate = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return ( ($item['working_date']==$date) && ($item['process_id']==$process_id) );
		}));
		$onlyProcessDateIds = array_column($onlyProcessDate, 'id');
		$onlyProcessBreakFlagDate = array_values(array_filter($allProcessBreakFlagDate, function($item) use ($onlyProcessDateIds){
			return (in_array($item['process_log_id'], $onlyProcessDateIds));
		}));
		$minuteOnlyProcess = array_sum(array_column($onlyProcessBreakFlagDate, 'total_minute'));
		$flagOnlyProcess = array_column($onlyProcessBreakFlagDate, 'break_flag');
		$flagString = "";
		foreach($flagOnlyProcess as $val){
			$flagString .= (empty($flagString))? $val:", ".$val;
		}
		/*echo "<pre>";print_r($flagOnlyProcess);echo "</pre>";
		echo "<pre>";print_r($flagString);echo "</pre>";
		echo "min=".$minuteOnlyProcess;*/
		return array("downtime"=>$minuteOnlyProcess, "remark"=>$flagString);
	}

	public function reportAjax(){
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
		// return "test";
	}

	function exportExcel($data, $shiftsAll, $resShiftSize, $total){
		/*echo "<pre>";print_r($data);echo "</pre>";
		$dataRes = array();
		$r = 3;
		foreach($data as $process_id=>$val){
			$dataRes[$r] = $data[$process_id]['process'];
			$r++;
			ksort($data[$process_id]['accum']);
			$dataRes[$r] = $data[$process_id]['accum'];
			$r++;
		}
		echo "<pre>";print_r($dataRes);echo "</pre>";*/
		/*$data2 = array(
			0 => array('id'=>1, 'name'=> 'test')
		);*/
		Excel::create('DailyReport', function($excel) use($data, $shiftsAll, $resShiftSize, $total) {
			$excel->sheet('Sheet 1', function($sheet) use($data, $shiftsAll, $resShiftSize, $total) {
				$colArr = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
				'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
				$hh[0] = array(0=>'Process name', 1=>'Cycle time', 2=>'Working day', 3=>'Target PC', 4=>'Input', 5=>'Output', 6=>'Yield NG(%)', 7=>'Yield Accum(%)', 8=>'BAL PC', 9=>'WIP', 10=>'Stock PRO', 11=>'Stock PC', 12=>'BAL Stock', 13=>'Result', (13+$resShiftSize)=>'Total NG', (13+$resShiftSize+5)=>'Price NG', (13+$resShiftSize+6)=>'Remark', (13+$resShiftSize+7)=>'Down time', (13+$resShiftSize+8)=>'Unit price'
				);
				$hh[1] = array();
				$colCurrent = 13;
				foreach($shiftsAll as $key=>$shift){
					$hh[1][$colCurrent] = $shift['label'];
					$hh[1][($colCurrent+1)] = 'NG1';
					$colCurrent = $colCurrent+2;
				}
				$hh[1][$colCurrent] = 'NG1';
				$hh[1][($colCurrent+1)] = 'NG2';
				$hh[1][($colCurrent+2)] = 'Setup';
				$hh[1][($colCurrent+3)] = 'D/T';
				$hh[1][($colCurrent+4)] = 'TTL';
				// $sheet->fromArray($data);
				$sheet->setAutoSize(false);
				$sheet->mergeCells($colArr[13].'1:'.$colArr[(13+$resShiftSize-1)].'1');
				$sheet->mergeCells($colArr[(13+$resShiftSize)].'1:'.$colArr[(13+$resShiftSize+4)].'1');
				$sheet->cell($colArr[13].'1', function($cell) {//for Result
					$cell->setAlignment('center');
				});
				$sheet->cell($colArr[(13+$resShiftSize)].'1', function($cell) {//for Total NG
					$cell->setAlignment('center');
				});
				$autoSize = array();
				$colFormat = array();
				for($c=0; $c<=(13+$resShiftSize+8); $c++){
					$sheet->cell($colArr[$c].'1', function($cell) use ($hh, $c){
						if(isset($hh[0][$c])) $cell->setValue($hh[0][$c]);
					});
					$sheet->cell($colArr[$c].'2', function($cell) use ($hh, $c){
						if(isset($hh[1][$c])) $cell->setValue($hh[1][$c]);
					});
					if( ($c<13) || ($c>=(13+$resShiftSize+5)) ){
						$sheet->mergeCells($colArr[$c].'1:'.$colArr[$c].'2');
						$sheet->cell($colArr[$c].'1', function($cell) {
							$cell->setValignment('center');
						});
						array_push($autoSize, $colArr[$c]);
						// $sheet->getColumnDimension($colArr[$c])->setAutoSize(false);
					} else {
						$sheet->getColumnDimension($colArr[$c])->setAutoSize(true);
					}
					if( in_array($c, array(1, (13+$resShiftSize+5), (13+$resShiftSize+8))) ){//cycle_time, price_ng, unit_price
						$colFormat[$colArr[$c]] = '0.00';
					}
				}
				$sheet->getColumnDimension('A')->setAutoSize(true);
				$sheet->setColumnFormat($colFormat);
				$sheet->row(1, function($row) use ($hh){
					$row->setFontWeight('bold');
				});
				$dataRes = array();
				$r = 3;
				foreach($data as $process_id=>$val){
					$mergeStr = "B".$r.":B".($r+1);
					$sheet->mergeCells($mergeStr);
					$sheet->cell("B".$r, function($cell) {
						$cell->setValignment('center');
						$cell->setAlignment('right');
					});
					$sheet->row($r, $data[$process_id]['process']);
					// $dataRes[$r] = $data[$process_id]['process'];
					$r++;
					ksort($data[$process_id]['accum']);
					$sheet->row($r, $data[$process_id]['accum']);
					// $dataRes[$r] = $data[$process_id]['accum'];
					$r++;
				}
				$rTotal[0] = array(
					0						=> 'Total cycle time',
					1						=> $total['cycle_time'],
					(13+$resShiftSize)		=> 'NG / Day',
					(13+$resShiftSize+4)	=> $total['day_ttl'],
					(13+$resShiftSize+5)	=> $total['day_price']
				);
				for($c=0; $c<=(13+$resShiftSize+8); $c++){
					$sheet->cell($colArr[$c].$r, function($cell) use ($rTotal, $c){
						if(isset($rTotal[0][$c])) $cell->setValue($rTotal[0][$c]);
					});
				}
				$sheet->mergeCells($colArr[2].$r.':'.$colArr[(13+$resShiftSize-1)].$r);
				$sheet->mergeCells($colArr[(13+$resShiftSize)].$r.':'.$colArr[(13+$resShiftSize+3)].$r);
				$sheet->mergeCells($colArr[(13+$resShiftSize+6)].$r.':'.$colArr[(13+$resShiftSize+8)].$r);
				$r++;
				$rTotal[1] = array(
					(13+$resShiftSize)		=> 'Accum NG',
					(13+$resShiftSize+4)	=> $total['accum_ttl'],
					(13+$resShiftSize+5)	=> $total['accum_price']
				);
				for($c=0; $c<=(13+$resShiftSize+8); $c++){
					$sheet->cell($colArr[$c].$r, function($cell) use ($rTotal, $c){
						if(isset($rTotal[1][$c])) $cell->setValue($rTotal[1][$c]);
					});
				}
				$sheet->mergeCells($colArr[0].$r.':'.$colArr[(13+$resShiftSize-1)].$r);
				$sheet->mergeCells($colArr[(13+$resShiftSize)].$r.':'.$colArr[(13+$resShiftSize+3)].$r);
				$sheet->mergeCells($colArr[(13+$resShiftSize+6)].$r.':'.$colArr[(13+$resShiftSize+8)].$r);
			});
		})->export('xls');
	}
}
