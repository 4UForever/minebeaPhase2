<?php

class AdminClearLogController extends AdminBaseController {

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
			$str .= "Clear finished";
		}
		return View::make('admins.reports.clearlog', compact('str'));
	}

}
