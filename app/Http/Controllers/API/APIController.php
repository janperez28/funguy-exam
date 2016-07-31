<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class APIController extends Controller
{
	/**
	 * Send an API response. 
	 * This method must be used when returning API responses via controller.
	 * TODO
	 * Use the constants for the HTTP code statuses of Symfony instead of the actual values.
	 *
	 * @param optional array $data
	 * @param optional string|array $message 
	 * @return Illuminate\Http\Response
	 */
	public function response($statusCode, $data = array(), $message = null)
	{x
		// Return a JSON response.
		return response()->json(compact('data', 'message'))->setStatusCode($statusCode);
	}	
	
	/**
	 * Shorthand for Controller::response(true, ...)
	 *
	 * @see Controller::response
	 */
	public function success($data = array(), $message = null)
	{
		return $this->response(200, $data, $message);
	}
	
	/**
	 * Shorthand for Controller::response(false, array, string)
	 *
	 * TODO
	 * It seems that we are using 400 as the "generic" error response code. 
	 * Checkout RFC and other REST guides/tutorials for proper status code to use based on the current operation.
	 *
	 * @see Controller::response
	 */
	public function error($message = null)
	{
		return $this->response(400, array(), $message);
	}
}