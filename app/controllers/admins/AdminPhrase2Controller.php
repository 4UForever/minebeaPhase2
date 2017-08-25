<?php
use Carbon\Carbon;

class AdminPhrase2Controller extends AdminBaseController
{
	public function reportImport(){
		return View::make('admins.reports.index');
	}

	public function reportDaily(){
		return View::make('admins.reports.daily');
	}
}
