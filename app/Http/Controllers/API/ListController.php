<?php

namespace App\Http\Controllers\API;

use DB;

class ListController extends APIController
{
	/**
	 * Handle request for list of nationalities.
	 *	
	 * @return Illuminate\Http\Response
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