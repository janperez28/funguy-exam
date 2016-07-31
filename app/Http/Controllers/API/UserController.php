<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\User;
use Validator;

class UserController extends APIController
{
	/**
	 * Handle user store request.
	 * 
	 * @param Request $request	 
	 */
	public function store(Request $request)
	{				
		$controller = $this;
	
		// Unfortunately, we cannot use the validate method of the ValidatesRequest trait
		// since we won't be able to set the response content once the exception is thrown.
		$params = $request->only('name', 'phone', 'nationality');				
		
		// A simple object to hold the existence test result
		$duplicate = new \stdClass;
		$duplicate->exists = false;
		
		// Validate fields then attach additional validation callback.
		$validator = Validator::make($params, array(
			'name' => 'required|max:255',
			// TODO
			// Consider adding a custom validation rule for phone numbers 			
			'phone' => 'required|digits:11',
			'nationality' => 'required|exists:nationalities,id'
		))->after(function($validator) use ($params, $duplicate) 
			{							
				if ($duplicate->exists = $this->recordExists($params))
				{
					$validator->errors()->add('name', trans('User already exists.'));
				}
			});
				
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
				// Let's pass the user_id given to the new user.
				return $this->success(array('user_id' => $user->id), trans('User was saved successfully.'));
			}

			// TODO
			// Not sure what exception to raise on these kind of situations
		}	
		// Return a 409 Conflict status code if record exists.
		// TODO
		// We may not need to return a duplicate error message since we are sending a Conflict response.		
		else if ($duplicate->exists)
		{
			return $this->response(409, array(), $validator->errors());
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
		}
		
		return $this->success($users->get()->toArray());
	}
	
	/**
	 * Additional validation for storing user record.
	 * Determines whether the user record (combination of the three fields) already exists.
	 * TODO
	 * Do we need to create a unique composite index using those columns?
	 *	 
	 * @param array $params
	 * @return boolean
	 */
	protected function recordExists($params)
	{		
		// Exists doesn't work, I wonder why?
		$user = User::where('name', '=', $params['name'])
			->where('phone', '=', $params['phone'])
			->where('nationality_id', '=', $params['nationality'])
			->first();
			
		return (boolean) $user;
	}
}