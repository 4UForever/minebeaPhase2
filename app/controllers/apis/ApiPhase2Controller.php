<?php

class ApiPhase2Controller extends ApiBaseController {

	public $success_msg = "Your request has successfully received.";
	public $failure_msg = "";

	function getShiftCode(){
		$qr_code = Input::get('qr_code');

		if(empty($qr_code)){
			$message = "You must provide qr_code";
			$status = 404;
			$data = null;
		} else {
			$message = $this->success_msg;
			$status = 200;
			$data = ShiftCode::all();
		}
		return Response::api($message, $status, $data);
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
			// print_r($lot_data->toArray());
			foreach($lot_data as $lot){
				$find_log_id = DB::table('lot_process')->where('lot_id', $lot->id)->where('sort', '1')->pluck('process_log_id');
				$find_process_log = ProcessLog::where('id', $find_log_id)->first(array('first_serial_no', 'last_serial_no'));
				// echo "log_id=".$find_log_id." last sn=".$find_process_log->first_serial_no."<br>";
				$lot->first_serial_no = (empty($find_process_log->first_serial_no))? "":strval($find_process_log->first_serial_no);
				$lot->last_serial_no = (empty($find_process_log->last_serial_no))? "":strval($find_process_log->last_serial_no);
			}
			// print_r($lot_data->toArray());
		}
		$lots = array('input_lot_number'=>$input_lot_number, 'lot_data'=>$lot_data);

		$message = "Your request has been successfully received.";
		return Response::api($message, 200, array('process_log'=>$arr_process_log, 'lots'=>$lots));
	}

	function modelData(){
		$qr_code = Input::get('qr_code');//8;
		$working_date = Input::get('working_date');
		$shift_id = Input::get('shift_id');
		$line_id = Input::get('line_id');//4;
		$product_id = Input::get('model_id');//4;
		$process_id = Input::get('process_id');//10;
		$process_log_from = Input::get('process_log_from');

		$check_data = array(
			"qr_code" => $qr_code,
			"working_date" => $working_date,
			"shift_id" => $shift_id,
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
			return Response::api("This user is already on process log ID : ".$arr_user->on_process, 409, array('process_log'=>$arr_user->on_process));
			// return Response::api("This user is already on process log ID : ".$arr_user->on_process, 409);
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
			return Response::api("Line: ".$line_id.", Model: ".$product_id.", Process id ".$process_id." does not exists in WIP conditions.", 404);
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

		$shift = ShiftCode::find($shift_id);
		// print_r($shift->toArray());

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
		$this->process_log->working_date = $working_date;
		$this->process_log->shift_id = $shift->id;
		$this->process_log->shift_label = $shift->label;
		$this->process_log->shift_time = $shift->time_string;
		$this->process_log->wip_id = $wip_sort->id;
		$this->process_log->wip_sort = $process_sort;
		// print_r($this->process_log->toArray());

		if ($this->process_log->save()) {
			$user = User::find($arr_user->id);
			$user->on_process = $this->process_log->id;
			$user->working_process = $this->process_log->process_id;
			$user->save();

			if(!empty($process_log_from)){
				DB::table('process_log_continues')
				->where('process_log_from', $process_log_from)
				->update([
					'process_log_to' => $this->process_log->id,
					'working_user_id' => $arr_user->id
				]);
			}
		} else {
			$errors = $this->process_log->errors()->all();
			return Response::api($errors, 404);
		}
		$arr_process_log = $this->process_log;
		$arr_process_log->model_id = $this->process_log->product_id;
		$arr_process_log->model_title = $this->process_log->product_title;
		unset($arr_process_log->product_id);
		unset($arr_process_log->product_title);
		return Response::api($this->success_msg, 200, array('process_log'=>$arr_process_log));
	}

	function processFinish(){
		$qr_code = Input::get('qr_code');
		$start_time = Input::get('start_time');
		$end_time = Input::get('end_time');
		$ok_qty = Input::get('ok_qty');
		$last_serial_no = Input::get('last_serial_no');
		$setup = Input::get('setup');
		$dt = Input::get('dt');
		$remark = Input::get('remark');
		$wip_qty = Input::get('wip_qty');
		$is_continue = Input::get('is_continue');

		$ngs_json = trim(Input::get('ngs'));//[{"ng_id":"1","ng_serial":"x00101","ng1":true,"ng2":false},{"ng_id":"2","ng_serial":"x00102","ng1":true,"ng2":true}]
		$ngs_arr = json_decode($ngs_json, true);
		$breaks_json = trim(Input::get('breaks'));//'[{"break_id":"1","break_flag":"test break flag 1","start_break":"2017-09-08 10:10:00","end_break":"2017-09-08 10:20:00"},{"break_id":"5","break_flag":"test break flag 5","start_break":"2017-09-08 11:10:00","end_break":"2017-09-08 11:20:00"}]';
		$breaks_arr = json_decode($breaks_json, true);
		$check_data = array(
			"qr_code" => $qr_code,
			"start_time" => $start_time,
			"end_time" => $end_time,
			"ok_qty" => $ok_qty,
			"last_serial_no" => $last_serial_no,
			"setup" => $setup,
			"dt" => $dt,
			"ngs" => $ngs_json,
			"breaks" => $breaks_json
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
			if(!$lot){
				return Response::api("This process does not have lot.", 404);
			}
			$lot_process_count = $lot->processes->count();
			if($lot_process_count > 0){
				return Response::api("Your process order is ".$wip_sort.", There are ".$lot_process_count." processes less your order are running. You can break or contact administrator.", 404);
			}

			if(is_array($ngs_arr) && is_array($breaks_arr)){
				//Save ng list
				foreach($ngs_arr as $key_ng=>$ng){
					$arr_ng = NgDetail::find($ng['ng_id']);
					if($ng['ng1']===TRUE){
						$process_log_ng1 = new ProcessLogNg1;
						$process_log_ng1->process_log_id = $arr_user->on_process;
						$process_log_ng1->ng_id = $arr_ng->id;
						$process_log_ng1->ng_title = $arr_ng->title;
						$process_log_ng1->ng_serial = $ng['ng_serial'];
						$process_log_ng1->quantity = 1;
						$process_log_ng1->save();
						// print_r($process_log_ng1->toArray());
					}
					if($ng['ng2']===TRUE){
						$process_log_ng = new ProcessLogNg;
						$process_log_ng->process_log_id = $arr_user->on_process;
						$process_log_ng->ng_id = $arr_ng->id;
						$process_log_ng->ng_title = $arr_ng->title;
						$process_log_ng->ng_serial = $ng['ng_serial'];
						$process_log_ng->quantity = 1;
						$process_log_ng->save();
						// print_r($process_log_ng->toArray());
					}
				}
				foreach($breaks_arr as $key_break=>$break){
					$arr_break = BreakReason::find($break['break_id']);
					$process_log_break = new ProcessLogBreak;
					$process_log_break->process_log_id = $arr_user->on_process;
					$process_log_break->break_id = $break['break_id'];
					$process_log_break->break_code = $arr_break->code;
					$process_log_break->break_reason = $arr_break->reason;
					$process_log_break->break_flag = empty($break['break_flag'])? "":$break['break_flag'];
					$process_log_break->start_break = $break['start_break'];
					$process_log_break->end_break = $break['end_break'];
					$process_log_break->total_minute = round(abs(strtotime($process_log_break->end_break) - strtotime($process_log_break->start_break)) / 60);
					$process_log_break->save();
					// print_r($process_log_break->toArray());
				}
				$process_log->setup = $setup;
				$process_log->dt = $dt;
				$process_log->last_serial_no = $last_serial_no;
				$process_log->start_time = $start_time;
				$process_log->end_time = $end_time;
				$process_log->ok_qty = $ok_qty;
				$process_log->wip_qty = empty($wip_qty)? null:$wip_qty;
				$process_log->ng1_qty = ProcessLogNg1::where('process_log_id', $arr_user->on_process)->sum('quantity');
				$process_log->ng_qty = ProcessLogNg::where('process_log_id', $arr_user->on_process)->sum('quantity');
				$process_log->total_break = ProcessLogBreak::where('process_log_id', $arr_user->on_process)->sum('total_minute');
				$process_log->total_minute = round(abs(strtotime($process_log->end_time) - strtotime($process_log->start_time)) / 60);
				$process_log->remark = $remark;
				$process_log->save();
				// print_r($process_log->toArray());
				//Update lot
				DB::table('lot_process')->where('lot_id', $process_log->lot_id)->where('process_id', $process_log->process_id)->where('process_log_id', $process_log->id)->update(array('qty'=>$ok_qty));
				if(strtoupper($is_continue)=="TRUE"){
					DB::table('process_log_continues')->insert([
						'process_log_from' => $process_log->id
					]);
					DB::table('lot_process')->insert([
						'lot_id' => $process_log->lot_id,
						'process_id' => $process_log->process_id,
						'sort' => $process_log->wip_sort
					]);
				} else {
					$wip = Wip::with('processes')->find($process_log->wip_id);
					if($process_log->wip_sort == $wip->processes->count()){
						$lot->quantity = $ok_qty;
						$lot->save();
					}
				}
				//Clear user process
				$user = User::find($arr_user->id);
				$user->on_process = NULL;
				$user->working_process = NULL;
				$user->save();

				$message = "Your request has been successfully received.";
				return Response::api($message, 200, array('process_log'=>$process_log));
			} else {
				return Response::api("You must provide ngs, breaks value as valid json format.", 404);
			}
		}
	}

	private function check_empty($data=array()){
		//use format $data = array("qr_code"=>$qr_code);
		foreach($data as $key=>$value){
			if(!empty($key)){
				if(!isset($value)) return "You must provide ".$key;
			}
		}
	}

	public function getContinueProcess(){
		$dbId = DB::table('process_log_continues')
				->where(function($query){
					$query->where('process_log_to', '')->orWhere('process_log_to', NULL);
				})
				->where('working_user_id', NULL)
				->orderBy('process_log_from')
				->get();
		$arrContinue = array_column(json_decode(json_encode($dbId), true), "process_log_from");
		// print_r($arrContinue);
		$data = ProcessLog::whereIn('id', $arrContinue)->get(['id as process_log_from', 'line_id', 'line_title', 'product_id', 'product_title', 'process_id', 'process_number', 'process_title', 'wip_id', 'wip_sort', 'lot_id', 'lot_number', 'line_leader'])->toArray();
		// print_r($data);
		$message = $this->success_msg;
		$status = 200;
		return Response::api($message, $status, $data);
	}
}
