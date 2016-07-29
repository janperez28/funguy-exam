<?php

namespace App\Http\Controllers;

use DB;

class ListController extends Controller
{
	/**
	 * Handle request for list of nationalities.
	 *	 
	 */
	public function nationalities()
	{
		// We do not have to create model for the Nationality records unless we need to.
		$records = DB::table('nationalities')
			->orderBy('name', 'asc')
			->get();
		
		$list = array();
		
		foreach ($records as $record)
		{
			$list[] = array('id' => $record->id, 'name' => $record->name);
		}
			
		return $this->success(compact('list'));
	}
}