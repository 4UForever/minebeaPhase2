<?php
use Carbon\Carbon;

class ApiProcessLogController extends ApiBaseController {

	protected $process_log;
	protected $process_log_part;
	protected $process_log_input;

	public function __construct(ProcessLog $process_log, ProcessLogPart $process_log_part, ProcessLogInput $process_log_input) {
		$this->process_log = $process_log;
		$this->process_log_part = $process_log_part;
		$this->process_log_input = $process_log_input;
	}

	public function check_empty($data=array()){
		//use format $data = array("qr_code"=>$qr_code);
		foreach($data as $key=>$value){
			if(!empty($key)){
				if(empty($value)) return "You must provide ".$key;
			}
		}
	}

	function modelData(){
		$qr_code = Input::get('qr_code');//8;
		$line_id = Input::get('line_id');//4;
		$product_id = Input::get('model_id');//4;
		$process_id = Input::get('process_id');//10;

		$check_data = array(
			"qr_code" => $qr_code,
			"line_id" => $line_id,
			"model_id" => $product_id,
			"process_id" => $process_id
		);
		$check_result = $this->check_empty($check_data);
		if(!empty($check_result)){
			return Response::api($check_result, 404);
		}

		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'email', 'first_name', 'last_name', 'on_process', 'working_process'));
		if(!empty($arr_user->on_process)){
			return Response::api("This user is already on process log ID : ".$arr_user->on_process, 409);
		}

		//Check process relation with user, line, model
		$eagor_load = array(
			'products' => function($query) use($product_id) {
				$query->where('products.id', $product_id);
			},
			'processes' => function($query) use($process_id) {
				$query->where('processes.id', $process_id);
			},
			'processes.users' => function($query) use($process_id, $arr_user) {
				$query->where('users.id', $arr_user->id);
			}
		);
		$line = Line::with($eagor_load)->find($line_id);
		if(empty($line)){
			return Response::api("Line id ".$line_id." does not exists.", 404);
		}// else { print_r($line->toArray()); }
		if($line->products->isEmpty()){
			return Response::api("Product id ".$product_id." does not belongs to line id ".$line_id, 404);
		}// else { print_r($line->products->toArray()); }
		if($line->processes->isEmpty()){
			return Response::api("Process id ".$process_id." does not belongs to line id ".$line_id, 404);
		}// else { print_r($line->processes->toArray()); }
		if($line->processes->first()->users->isEmpty()){
			return Response::api("User code ".$qr_code." does not belongs to process id ".$process_id, 404);
		}

		//Check processes exists in WIP and find sort of process
		$wip_sort = Wip::where('line_id', $line_id)->where('product_id', $product_id)
			->with(array('processes'=>function($q) use($process_id) {
				$q->where('process_id', $process_id);
			}))
			->whereHas('processes', function($q) use($process_id) {
				$q->where('process_id', $process_id);
			})->first();
		if(empty($wip_sort)){
			return Response::api("Process id ".$process_id." does not exists in WIP conditions.", 404);
		}// else { print_r($wip_sort->toArray()); }
		$process_sort = $wip_sort->processes->first()->pivot->sort;

		//Check process exists working by any user
		$user_process_exist = User::where('working_process', $process_id)->first();
		if(!empty($user_process_exist)){
			//print_r($user_process_exist->toArray());
			return Response::api("Process id ".$process_id." has now working by user id ".$user_process_exist->id." (".$user_process_exist->first_name." ".$user_process_exist->last_name.")", 404);
		}

		//Check lot exists
		$lot = Lot::where('wip_id', $wip_sort->id)
				->whereHas('processes', function($q) use($process_id) {
					$q->where('process_id', $process_id)->whereNull('process_log_id');
				})
				->whereNull('quantity')->get();
		if($lot->isEmpty()){
			if($process_sort>1){
				return Response::api("Process id ".$process_id." is order ".$process_sort." for WIP ".$wip_sort->title.", Please contact process order 1 to create Lot", 404);
			}
		}
		//print_r($lot->toArray());

		//Start work -> Insert process log
		$this->process_log = new ProcessLog;
		$this->process_log->user_id = $arr_user->id;
		$this->process_log->full_name = $arr_user->first_name." ".$arr_user->last_name;
		$this->process_log->user_email = $arr_user->email;
		$this->process_log->line_id = $line->id;
		$this->process_log->line_title = $line->title;
		$this->process_log->product_id = $line->products->first()->id;
		$this->process_log->product_title = $line->products->first()->title;
		$this->process_log->process_id = $line->processes->first()->id;
		$this->process_log->process_number = $line->processes->first()->number;
		$this->process_log->process_title = $line->processes->first()->title;
		$this->process_log->wip_id = $wip_sort->id;
		$this->process_log->wip_sort = $process_sort;
		//print_r($this->process_log->toArray());

		if ($this->process_log->save()) {
			$user = User::find($arr_user->id);
			$user->on_process = $this->process_log->id;
			$user->working_process = $this->process_log->process_id;
			$user->save();
		} else {
			$errors = $this->process_log->errors()->all();
			return Response::api($errors, 404);
		}

		$message = "Your request has been successfully submitted.";
		return Response::api($message, 200, array('process_log_id'=>$this->process_log->id));
	}

	function requestPartList(){
		$qr_code = Input::get('qr_code');
		$product_id = Input::get('model_id');
		$process_id = Input::get('process_id');

		$check_data = array(
			"qr_code" => $qr_code,
			"model_id" => $product_id,
			"process_id" => $process_id
		);
		$check_result = $this->check_empty($check_data);
		if(!empty($check_result)){
			return Response::api($check_result, 404);
		}

		$arr_product = Product::where('id', $product_id)->first(array('id', 'title'));
		if(empty($arr_product)){
			return Response::api("Model ID : ".$product_id." does not exists", 404);
		}
		$arr_process = Process::where('id', $process_id)->first(array('id', 'title', 'number'));
		if(empty($arr_process)){
			return Response::api("Process ID : ".$process_id." does not exists", 404);
		}
		//return part list
		$arr_part = Part::with('iqc_lots')->where('product_id', $product_id)->where('process_id', $process_id)->get();
		//print_r($arr_part->toArray());
		$message = "Your request has been successfully submitted.";
		return Response::api($message, 200, array('parts'=>$arr_part));
	}

	function keepFirstSerial(){
		$qr_code = Input::get('qr_code');
		$line_leader = Input::get('line_leader');
		$first_serial_no = Input::get('first_serial_no');

		$check_data = array(
			"qr_code" => $qr_code,
			"line_leader" => $line_leader,
			"first_serial_no" => $first_serial_no
		);
		$check_result = $this->check_empty($check_data);
		if(!empty($check_result)){
			return Response::api($check_result, 404);
		}

		$arr_leader = User::where('id', $line_leader)->first(array('id', 'first_name', 'last_name'));
		if(empty($arr_leader)){
			return Response::api("Line leader ID : ".$line_leader." does not exists", 404);
		}

		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'on_process'));
		//print_r($arr_user->toArray());
		if(!empty($arr_user->on_process)){
			$process_log = $this->process_log->find($arr_user->on_process);
			//Create lot if sort==1
			$lot_id = Input::get('lot_id');
			$lot_number = Input::get('lot_number');
			if($process_log->wip_sort == 1){
				if(empty($lot_number)){
					return Response::api("You must provide lot_number", 404);
				}
				$lot_exist = Lot::where('number', $lot_number)->get()->count();
				if($lot_exist>0){
					return Response::api("Lot number ".$lot_number." has exist in database.", 404);
				}
				$wip = Wip::find($process_log->wip_id);
				$lot = new Lot(array('wip_id'=>$wip->id, 'wip_title'=>$wip->title, 'number'=>$lot_number));
				if($lot->save()) {
					foreach($wip->processes as $process){
						$lot->processes()->attach($process->pivot->process_id, ['sort' => $process->pivot->sort]);
					}
				}
			} else {
				if(empty($lot_id)){
					return Response::api("You must provide lot_id", 404);
				}
				$lot = Lot::find($lot_id);
				if(empty($lot)){
					return Response::api("Lot id ".$lot_id." does not exist in database.", 404);
				}
			}
			DB::table('lot_process')->where('lot_id', $lot->id)->where('process_id', $process_log->process_id)->whereNull('process_log_id')->update(array('process_log_id' => $process_log->id));
			//$lot->processes()->updateExistingPivot($process_log->process_id, array('process_log_id'=>$process_log->id));

			$process_log->lot_id = $lot->id;
			$process_log->lot_number = $lot->number;
			$process_log->line_leader = $arr_leader->id;
			$process_log->line_leader_name = $arr_leader->first_name." ".$arr_leader->last_name;
			$process_log->first_serial_no = $first_serial_no;
			$process_log->save();

			$message = "Your request has been successfully submitted.";
			return Response::api($message, 200, array('process_log_id'=>$process_log->id));
		} else {
			return Response::api("This user does not on any process ", 404);
		}
	}

	function recoverWorkingStatus(){
		$qr_code = Input::get('qr_code');

		if(empty($qr_code)){
			return Response::api("You must provide qr_code", 404);
		}

		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'email', 'first_name', 'last_name', 'on_process'));
		if(!empty($arr_user->on_process)){
			$arr_process_log = ProcessLog::where('id', $arr_user->on_process)->first(array('id', 'user_id', 'user_email', 'line_id', 'line_title', 'product_id as model_id', 'product_title as model_title', 'process_id', 'process_number', 'process_title', 'working_date', 'shift_id', 'shift_label', 'shift_time', 'wip_id', 'wip_sort', 'lot_id', 'lot_number', 'on_break'));
			//print_r($arr_process_log->toArray());
			if(empty($arr_process_log->lot_id)){
				$parts = array();
				$wip_lots = array();
				$arr_parts = ProcessLogPart::where('process_log_id', $arr_process_log->id)->get();
				if(!$arr_parts->isEmpty()){
					//print_r($arr_parts->toArray());
					foreach($arr_parts as $key=>$part_ids){
						$parts[$key]['number'] = $part_ids->part_number;
						$parts[$key]['iqc_lots'] = array();
						$arr_iqcs = ProcessLogInput::where('process_log_id', $arr_process_log->id)->where('lot_type', 'IQC')->where('part_id', $part_ids->part_id)->get();
						if(!$arr_iqcs->isEmpty()){
							//print_r($arr_iqcs->toArray());
							foreach($arr_iqcs as $iqc_ids){
								array_push($parts[$key]['iqc_lots'], array("number"=>$iqc_ids->lot_number, "quantity"=>$iqc_ids->use_qty));
							}
						}
					}
				}
				//print_r($parts);
				$arr_wips = ProcessLogInput::where('process_log_id', $arr_process_log->id)->where('lot_type', 'WIP')->get();
				if(!$arr_wips->isEmpty()){
					//print_r($arr_wips->toArray());
					foreach($arr_wips as $wip_ids){
						array_push($wip_lots, array("number"=>$wip_ids->lot_number, "quantity"=>$wip_ids->use_qty));
					}
				}
				//print_r($wip_lots);
				$working_page = FALSE;
				$message = "Your request has been successfully received.";
				return Response::api($message, 200, array('process_log'=>$arr_process_log, 'working_page'=>$working_page, 'parts'=>$parts, 'wip_lots'=>$wip_lots));
			} else {
				$working_page = TRUE;
				$message = "Your request has been successfully received.";
				return Response::api($message, 200, array('process_log'=>$arr_process_log, 'working_page'=>$working_page));
			}
		} else {
			return Response::api("This user does not on any process ", 404);
		}
	}

	function checkIqcLot(){
		$qr_code = Input::get('qr_code');
		$lot_number = Input::get('lot_number');

		if(empty($qr_code)){
			return Response::api("You must provide qr_code", 404);
		}
		if(empty($lot_number)){
			return Response::api("You must provide lot_number", 404);
		}

		$iqc_count = IqcLot::where('number', $lot_number)->count();
		$message = "Your request has been successfully received.";
		return Response::api($message, 200, array('count'=>$iqc_count));
	}

	function checkWipLot(){
		$qr_code = Input::get('qr_code');
		$lot_number = Input::get('lot_number');

		if(empty($qr_code)){
			return Response::api("You must provide qr_code", 404);
		}
		if(empty($lot_number)){
			return Response::api("You must provide lot_number", 404);
		}

		$lot_count = Lot::where('number', $lot_number)->count();
		$message = "Your request has been successfully received.";
		return Response::api($message, 200, array('count'=>$lot_count));
	}

	function checkInputLot(){
		$qr_code = Input::get('qr_code');
		$parts_json = trim(Input::get('parts'));
		$wip_lots_json = trim(Input::get('wip_lots'));
		$parts = json_decode($parts_json, true);
		$wip_lots = json_decode($wip_lots_json, true);
		/*
		$parts = '[{"number":"2222","iqc_lots":[{"number":"iqc001","quantity":10}]}]';
		$wip_lots = '[{"number":"aa","quantity":"10"},{"number":"dddd","quantity":"20"}]';
		print_r($parts);
		print_r($wip_lots);
		*/
		if(empty($qr_code)){
			return Response::api("You must provide qr_code", 404);
		}

		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'on_process'));
		if(empty($arr_user->on_process)){
			return Response::api("This user does not on any process.", 404);
		} else {
			if(is_array($parts)){
				foreach($parts as $key_part=>$part){
					$arr_part = Part::where('number', $part['number'])->first(array('id', 'number', 'name'));
					if(empty($arr_part)){
						return Response::api("Part number ".$part['number']." does not exists", 404);
					} else {
						if(is_array($part['iqc_lots'])){
							foreach($part['iqc_lots'] as $key_iqc=>$iqc_lot){
								$arr_iqc = IqcLot::where('part_id', $arr_part->id)->where('number', $iqc_lot['number'])->first(array('id', 'number', 'quantity'));
								if(empty($arr_iqc)){
									return Response::api("IQC lot number ".$iqc_lot['number']." does not exists in Part number ".$part['number'], 404);
								} else {
									if($arr_iqc['quantity'] < $iqc_lot['quantity']){
										return Response::api("IQC lot number ".$iqc_lot['number']." has quantity ".$arr_iqc['quantity'], 404);
									}
								}
							}
						}
					}
				}//end foreach parts
			} else {
				return Response::api("You must provide parts value as valid json format.", 404);
			}

			if(is_array($wip_lots)){
				foreach($wip_lots as $key_wip=>$wip_lot){
					$arr_wip = Lot::where('number', $wip_lot['number'])->whereNotNull('quantity')->first(array('id', 'number', 'quantity'));
					if(empty($arr_wip)){
						return Response::api("WIP lot number ".$wip_lot['number']." does not exists", 404);
					} else {
						if($arr_wip['quantity'] < $wip_lot['quantity']){
							return Response::api("WIP lot number ".$wip_lot['number']." has quantity ".$arr_wip['quantity'], 404);
						}
					}
				}
			} else {
				return Response::api("You must provide wip_lots value as valid json format.", 404);
			}
		}

		ProcessLogPart::where('process_log_id', $arr_user->on_process)->delete();
		ProcessLogInput::where('process_log_id', $arr_user->on_process)->delete();
		if(is_array($parts)){
			foreach($parts as $key_part=>$part){
				$arr_part = Part::where('number', $part['number'])->first(array('id', 'number', 'name'));

				$process_log_part = ProcessLogPart::where('process_log_id', $arr_user->on_process)->where('part_id', $arr_part->id)->first();
				if(empty($process_log_part)){
					$process_log_part = new ProcessLogPart;
					$process_log_part->process_log_id = $arr_user->on_process;
					$process_log_part->part_id = $arr_part->id;
					$process_log_part->part_number = $arr_part->number;
					$process_log_part->part_name = $arr_part->name;
					$process_log_part->save();
					//print_r($process_log_part->toArray());
				}

				if(is_array($part['iqc_lots'])){
					foreach($part['iqc_lots'] as $key_iqc=>$iqc_lot){
						$arr_iqc = IqcLot::where('number', $iqc_lot['number'])->first(array('id', 'number'));

						$process_log_iqc = ProcessLogInput::where('process_log_id', $arr_user->on_process)->where('lot_type', 'IQC')->where('lot_id', $arr_iqc->id)->first();
						if(empty($process_log_iqc)){
							$process_log_iqc = new ProcessLogInput;
							$process_log_iqc->process_log_id = $arr_user->on_process;
							$process_log_iqc->part_id = $arr_part->id;
							$process_log_iqc->lot_type = "IQC";
							$process_log_iqc->lot_id = $arr_iqc->id;
							$process_log_iqc->lot_number = $arr_iqc->number;
							$process_log_iqc->use_qty = $iqc_lot['quantity'];
							$process_log_iqc->save();
							//print_r($process_log_iqc->toArray());
						}
					}
				}
			}//end foreach parts
		}//end parts
		if(is_array($wip_lots)){
			foreach($wip_lots as $key_wip=>$wip_lot){
				$arr_wip = Lot::where('number', $wip_lot['number'])->first(array('id', 'number'));

				$process_log_wip = ProcessLogInput::where('process_log_id', $arr_user->on_process)->where('lot_type', 'WIP')->where('lot_id', $arr_wip->id)->first();
				if(empty($process_log_wip)){
					$process_log_wip = new ProcessLogInput;
					$process_log_wip->process_log_id = $arr_user->on_process;
					$process_log_wip->part_id = NULL;
					$process_log_wip->lot_type = "WIP";
					$process_log_wip->lot_id = $arr_wip->id;
					$process_log_wip->lot_number = $arr_wip->number;
					$process_log_wip->use_qty = $wip_lot['quantity'];
					$process_log_wip->save();
					//print_r($process_log_wip->toArray());
				}
			}
		}

		$arr_process_log = ProcessLog::where('id', $arr_user->on_process)->first(array('id', 'user_id', 'user_email', 'line_id', 'line_title', 'product_id as model_id', 'product_title as model_title', 'process_id', 'process_number', 'process_title', 'wip_id', 'wip_sort'));
		$process_id = $arr_process_log->process_id;
		if($arr_process_log->wip_sort == 1){
			$input_lot_number = TRUE;
			$lot_data = array();
		} else {
			$input_lot_number = FALSE;
			$lot_data = Lot::where('wip_id', $arr_process_log->wip_id)
				->whereHas('processes', function($q) use($process_id) {
					$q->where('process_id', $process_id)->whereNull('process_log_id');
				})
				->whereNull('quantity')
				->get();
		}
		$lots = array('input_lot_number'=>$input_lot_number, 'lot_data'=>$lot_data);

		$message = "Your request has been successfully received.";
		return Response::api($message, 200, array('process_log'=>$arr_process_log, 'lots'=>$lots));
	}

	function breakList(){
		$qr_code = Input::get('qr_code');

		if(empty($qr_code)){
			return Response::api("You must provide qr_code", 404);
		}

		$break_arr = BreakReason::all();
		$message = "Your request has been successfully received.";
		return Response::api($message, 200, array('breaks'=>$break_arr));
	}

	function ngList(){
		$qr_code = Input::get('qr_code');

		if(empty($qr_code)){
			return Response::api("You must provide qr_code", 404);
		}

		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'on_process'));
		if(empty($arr_user->on_process)){
			return Response::api("This user does not on any process.", 404);
		} else {
			$arr_process_log = ProcessLog::find($arr_user->on_process);
			if(empty($arr_process_log)){
				return Response::api("Process log ID : ".$arr_user->on_process." does not exists", 404);
			}
			$ng_arr = NgDetail::where('process_id', $arr_process_log->process_id)->get();

			if(! $ng_arr->toArray()){
				$message = "Your working process not have any NG list, Please contact administrator.";
			} else {
				$message = "Your request has been successfully received.";
			}
			return Response::api($message, 200, array('process_id'=>$arr_process_log->process_id, 'ngList'=>$ng_arr));
		}
	}

	function processBreak(){
		$qr_code = Input::get('qr_code');
		$break_id = Input::get('break_id');

		$check_data = array(
			"qr_code" => $qr_code,
			"break_id" => $break_id
		);
		$check_result = $this->check_empty($check_data);
		if(!empty($check_result)){
			return Response::api($check_result, 404);
		}

		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'on_process'));
		if(empty($arr_user->on_process)){
			return Response::api("This user does not on any process.", 404);
		} else {
			$arr_break = BreakReason::find($break_id);

			$process_log_break = new ProcessLogBreak;
			$process_log_break->process_log_id = $arr_user->on_process;
			$process_log_break->break_id = $arr_break->id;
			$process_log_break->break_code = $arr_break->code;
			$process_log_break->break_reason = $arr_break->reason;
			$process_log_break->start_break = date("Y-m-d H:i:s");
			$process_log_break->save();

			$process_log = $this->process_log->find($arr_user->on_process);
			$process_log->on_break = $process_log_break->id;
			$process_log->save();

			$message = "Your request has been successfully received.";
			return Response::api($message, 200, array('process_log_break'=>$process_log_break));
		}
	}

	function processStart(){
		$qr_code = Input::get('qr_code');

		if(empty($qr_code)){
			return Response::api("You must provide qr_code", 404);
		}

		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'on_process'));
		if(empty($arr_user->on_process)){
			return Response::api("This user does not on any process.", 404);
		} else {
			$arr_process_log = ProcessLog::find($arr_user->on_process);
			if(empty($arr_process_log->on_break)){
				if(empty($arr_process_log->start_time)){
					$process_log = ProcessLog::find($arr_user->on_process);
					$process_log->start_time = date("Y-m-d H:i:s");
					$process_log->save();
				}
			} else {
				$process_log_break = ProcessLogBreak::find($arr_process_log->on_break);
				$process_log_break->end_break = date("Y-m-d H:i:s");
				$process_log_break->total_minute = round(abs(strtotime($process_log_break->end_break) - strtotime($process_log_break->start_break)) / 60);
				$process_log_break->save();

				//Clear process log on_break
				$arr_process_log->on_break = NULL;
				$arr_process_log->save();
			}
		}
		$message = "Your request has been successfully received.";
		return Response::api($message, 200);
	}

	function processFinish(){
		$qr_code = Input::get('qr_code');

		$ok_qty = Input::get('ok_qty');
		$last_serial_no = Input::get('last_serial_no');
		$ngs_json = trim(Input::get('ngs'));
		$ngs_arr = json_decode($ngs_json, true);

		$check_data = array(
			"qr_code" => $qr_code,
			"ok_qty" => $ok_qty,
			"last_serial_no" => $last_serial_no
		);
		$check_result = $this->check_empty($check_data);
		if(!empty($check_result)){
			return Response::api($check_result, 404);
		}

		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'on_process'));
		if(empty($arr_user->on_process)){
			return Response::api("This user does not on any process.", 404);
		} else {
			$process_log = ProcessLog::find($arr_user->on_process);
			//Check lot
			$wip_sort = $process_log->wip_sort;
			$lot = Lot::with(array('processes'=>function($q) use($wip_sort) {
				$q->where('sort', '<', $wip_sort)->whereNull('qty');
			}))->find($process_log->lot_id);
			$lot_process_count = $lot->processes->count();
			if($lot_process_count > 0){
				return Response::api("Your process order is ".$wip_sort.", There are ".$lot_process_count." processes less your order are running. You can break or contact administrator.", 404);
			}

			if(is_array($ngs_arr)){
				//Save ng list
				foreach($ngs_arr as $key_ng=>$ng){
					$arr_ng = NgDetail::find($ng['ng_id']);
					$process_log_ng = new ProcessLogNg;
					$process_log_ng->process_log_id = $arr_user->on_process;
					$process_log_ng->ng_id = $arr_ng->id;
					$process_log_ng->ng_title = $arr_ng->title;
					$process_log_ng->quantity = $ng['quantity'];
					$process_log_ng->save();
					//print_r($process_log_ng->toArray());
				}

				$process_log->last_serial_no = $last_serial_no;
				$process_log->end_time = date("Y-m-d H:i:s");
				$process_log->ok_qty = $ok_qty;
				$process_log->ng_qty = ProcessLogNg::where('process_log_id', $arr_user->on_process)->sum('quantity');
				$process_log->total_break = ProcessLogBreak::where('process_log_id', $arr_user->on_process)->sum('total_minute');
				$process_log->total_minute = round(abs(strtotime($process_log->end_time) - strtotime($process_log->start_time)) / 60);
				$process_log->save();
				//print_r($process_log->toArray());

				//Update lot
				DB::table('lot_process')->where('lot_id', $process_log->lot_id)->where('process_id', $process_log->process_id)->where('process_log_id', $process_log->id)->update(array('qty'=>$ok_qty));
				$wip = Wip::with('processes')->find($process_log->wip_id);
				if($process_log->wip_sort == $wip->processes->count()){
					$lot->quantity = $ok_qty;
					$lot->save();
				}

				//Clear user process
				$user = User::find($arr_user->id);
				$user->on_process = NULL;
				$user->working_process = NULL;
				$user->save();

				$message = "Your request has been successfully received.";
				return Response::api($message, 200, array('process_log'=>$process_log));
			} else {
				return Response::api("You must provide ngs value as valid json format.", 404);
			}
		}
	}

	function processClear(){
		$qr_code = Input::get('qr_code');

		if(empty($qr_code)){
			return Response::api("You must provide qr_code", 404);
		}
		$arr_user = User::where('qr_code', $qr_code)->first(array('id', 'on_process'));
		if(empty($arr_user->on_process)){
			return Response::api("This user does not on any process.", 404);
		} else {
			$process_log = ProcessLog::find($arr_user->on_process);
			if(empty($process_log->lot_id)){
				$user = User::find($arr_user->id);
				$user->on_process = NULL;
				$user->working_process = NULL;
				$user->save();
				$message = "Successfully clear process.";
				return Response::api($message, 200);
			} else {
				$message = "Can not clear process cause lot_number was created. So you need to finish this process.";
				return Response::api($message, 404);
			}
		}
	}

}
