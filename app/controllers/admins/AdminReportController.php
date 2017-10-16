<?php
use Carbon\Carbon;

class AdminReportController extends AdminBaseController
{
	public function reportDaily(){
		$date = Input::get('date');
		$line_id = Input::get('line_id');
		$model_id = Input::get('model_id');
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
			// echo "<pre>";print_r($process_obj->toArray());echo "</pre>";
			foreach($process_date as $key=>$process_arr){
				// echo "process=".$process_arr['process_id']." number=".$process_arr['process_number']."<br>";
				// if($process_arr['process_id']=="10"){
					$process_id = $process_arr['process_id'];
					$timeNprice = $this->getTimeNprice($timePriceAll, $date, $line_id, $model_id, $process_id);
					$targetNstock = $this->getTargetNstock($targetAll, $date, $line_id, $model_id, $process_id);

					$data[$process_id]['process'][0] = $process_arr['process_title'];
					$data[$process_id]['accum'][0] = "Accum (".$process_arr['process_number'].")";
					$data[$process_id]['process'][1] = $timeNprice['cycle_time'];
					$data[$process_id]['process'][2] = $this->getWorkingDay($process_logs, $date, $line_id, $model_id, $process_id);
					$data[$process_id]['process'][3] = $targetNstock['target_pc'];
					$data[$process_id]['accum'][3] = $this->getAccumTarget($targetAll, $date, $line_id, $model_id, $process_id);
					$data[$process_id]['process'][4] = $this->getInput($process_logs, $date, $process_id);
					$data[$process_id]['process'][5] = $this->getOutput($process_logs, $date, $process_id);
					$data[$process_id]['process'][6] = $this->getYieldNg($process_logs, $date, $process_id);
					$data[$process_id]['process'][7] = "wait";
					$data[$process_id]['process'][8] = $data[$process_id]['process'][5] - $data[$process_id]['process'][3];//output - target pc
					$data[$process_id]['process'][9] = "wait";
					$data[$process_id]['process'][10] = $targetNstock['stock_pc'];
					$data[$process_id]['process'][11] = "wait";
					//result of shift Ng
					$shiftSum = $this->getShiftSum($process_logs, $shiftsAll, $date, $process_id);
					$colCurrent = 12;
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
			$total['cycle_time'] = array_sum(array_column(array_column($data, 'process'), 1));
			$total['day_ttl'] = array_sum(array_column(array_column($data, 'process'), ($colCurrent+4)));
			$total['day_price'] = array_sum(array_column(array_column($data, 'process'), ($colCurrent+5)));
			$total['accum_ttl'] = array_sum(array_column(array_column($data, 'accum'), ($colCurrent+4)));
			$total['accum_price'] = array_sum(array_column(array_column($data, 'accum'), ($colCurrent+5)));
		}
		// echo "<pre>";print_r($data);echo "</pre>";
		//make function get all import price and get all import target
		return View::make('admins.reports.daily', compact('date', 'model_id', 'line_id', 'model_title', 'line_title', 'models', 'lines', 'data', 'shiftsAll', 'resShiftSize', 'total'));
	}

	public function reportDaily1(){//for test
		$date = "2017-10-06";
		$line_id = "6";
		$model_id = "7";
		$process_id = "10";
		$process_logs = $this->getAllProcessLog($date, $line_id, $model_id);
		$allProcessBreakFlagDate = $this->getAllProcessBreakFlagDate($process_logs, $date);

		$get = $this->getDownTimeSum($process_logs, $allProcessBreakFlagDate, $date, $process_id);
		echo "<pre>";print_r($get);echo "</pre>";
		//SELECT * FROM `process_log_breaks` WHERE process_log_id in (SELECT id FROM `process_logs` WHERE working_date='2017-10-06' and line_id='6' and product_id='7') and break_id in (SELECT id FROM break_reasons WHERE flag='1')
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
									'working_date' => $val['working_date']
								)
					);
				}
			}
		}
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

	function getOutput($process_logs, $date, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return (($item['working_date']==$date) &&
			($item['process_id']==$process_id));
		}));
		$res = array_sum(array_column($filter, 'ok_qty'));
		return $res;
	}

	function getYieldNg($process_logs, $date, $process_id){
		$filter = array_values(array_filter($process_logs, function($item) use ($date, $process_id){
			return (($item['working_date']==$date) &&
			($item['process_id']==$process_id));
		}));
		$sumOk = array_sum(array_column($filter, 'ok_qty'));
		$sumNg = array_sum(array_column($filter, 'ng_qty'));
		$res = (($sumOk+$sumNg)>0)? ($sumNg / ($sumOk+$sumNg))*100:0;
		return number_format($res, 2);
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
}
