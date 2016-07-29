<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class UserController extends Controller
{
	/**
	 * Handle user store request.
	 * 
	 * @param Request
	 * @return Response
	 */
	public function store(Request $request)
	{	
		// TODO
		// We may want to use the validate method of the ValidatesRequest trait and
		// let the exception handler do the necessary action of sending the
		// JSON error response.		
		// For now, we will make use of the facade.
		$params = $request->only('name', 'phone', 'nationality');		
		
		$validator = Validator::make($params, array(
			'name' => 'required|max:255',
			// TODO
			// Consider adding a custom validation rule for phone numbers 			
			'phone' => 'required|digits:11',
			'nationality' => 'required|exists:nationalities,id'
		));		
				
		if ($validator->passes())
		{				
			// Let's save the user.
			$user = new User();
						
			// We may want to allow mass assignment if we are dealing more fields.
			$user->name = $params['name'];
			$user->phone = $params['phone'];
			$user->nationality_id = $params['nationality'];
			
			$user->save();
		
			return $this->success(array(), trans("User was saved successfully."));
		}
		
		// Display error messages
		return $this->error($validator->errors());
	}
}