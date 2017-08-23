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

	function modelData(){
		$qr_code = Input::get('qr_code');//8;
		$shift_id = Input::get('shift_id');
		$line_id = Input::get('line_id');//4;
		$product_id = Input::get('model_id');//4;
		$process_id = Input::get('process_id');//10;
		$dt = Input::get('dt');
		$setup = Input::get('setup');

		$check_data = array(
			"qr_code" => $qr_code,
			"shift_id" => $shift_id,
			"line_id" => $line_id,
			"model_id" => $product_id,
			"process_id" => $process_id,
			"dt" => $dt,
			"setup" => $setup
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
		$this->process_log->shift_id = $shift->id;
		$this->process_log->shift_label = $shift->label;
		$this->process_log->shift_time = $shift->time_string;
		$this->process_log->dt = $dt;
		$this->process_log->setup = $setup;
		$this->process_log->wip_id = $wip_sort->id;
		$this->process_log->wip_sort = $process_sort;
		// print_r($this->process_log->toArray());

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

	function processBreak(){
		$qr_code = Input::get('qr_code');
		$break_id = Input::get('break_id');
		$break_flag = Input::get('break_flag');

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
			$process_log_break->break_flag = $break_flag;
			$process_log_break->start_break = date("Y-m-d H:i:s");
			$process_log_break->save();

			$process_log = $this->process_log->find($arr_user->on_process);
			$process_log->on_break = $process_log_break->id;
			$process_log->save();

			$message = "Your request has been successfully received.";
			return Response::api($message, 200, array('process_log_break'=>$process_log_break));
		}
	}

	private function check_empty($data=array()){
		//use format $data = array("qr_code"=>$qr_code);
		foreach($data as $key=>$value){
			if(!empty($key)){
				if(empty($value)) return "You must provide ".$key;
			}
		}
	}

}
