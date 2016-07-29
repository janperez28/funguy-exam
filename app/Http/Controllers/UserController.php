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
	 * @param Request $request	 
	 */
	public function store(Request $request)
	{				
		// Unfortunately, we cannot use the validate method of the ValidatesRequest trait
		// since we won't be able to set the response content once the exception is thrown.
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
			
			if ($user->save())
			{		
				return $this->success(array(), trans("User was saved successfully."));
			}

			// TODO
			// Not sure what exception to raise on these kind of situations
		}
		
		// Not sure if we need to use 422 status code here.
		return $this->error($validator->errors());
	}
	
	/**
	 * Handle delete user requests.
	 * 
	 * @param int $userId
	 * @return Illuminate\Http\Response
	 */
	public function destroy($userId)
	{
		$user = User::findOrFail($userId);
				
		if ($user->delete())
		{
			// We could also send a 204 here but it should not send any content :(
			return $this->success();
		}
				
		// A 409 might be appropriate on this stuation.
		return $this->error("Cannot delete user record.");
	}
	
	/**
	 * Handle list users request.
	 *
	 * @param Illuminate\Http\Request $request
	 * @return Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$users = User::orderBy('id', 'desc');		
		$params = $request->only('last_id', 'limit');
		
		$validator = Validator::make($params, array(
			'last_id' => 'exists:' . with(new User)->getTable() . ',id',
			'limit' => 'number|min:1|max:20'
		));
		
		if ($validator->passes())
		{	
			// Add query filter for start id.
			if ($params['last_id'])
			{
				$users->where('id', '>', $params['last_id']);
			}
			print_r($params['limit']);
			
			$users->take($params['limit']);
		}
		
		return $this->success($users->get()->toArray());
	}
}