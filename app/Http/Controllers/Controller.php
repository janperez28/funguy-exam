<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;
	
	/**
	 * Send an API response. 
	 * This method must be used when returning API responses via controller.
	 *
	 * @param boolean $success
	 * @param optional array $data
	 * @param optional string|array $message 
	 * @return Illuminate\Http\Response
	 */
	public function response($success, $data = array(), $message = null)
	{
		// Return a JSON response.
		return response()->json(compact('success', 'data', 'message'));
	}
	
	/**
	 * Shorthand for Controller::response(true, ...)
	 *
	 * @see Controller::response
	 */
	public function success($data = array(), $message = null)
	{
		return $this->response(true, $data, $message);
	}
	
	/**
	 * Shorthand for Controller::response(false, array, string)
	 *
	 * @see Controller::response
	 */
	public function error($message)
	{
		return $this->response(false, array(), $message);
	}	
}
