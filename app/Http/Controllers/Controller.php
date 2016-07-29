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
	 * @param optional string $error
	 * @return array
	 */
	public function response($success, $data = array(), $error = null)
	{
		// Fortunately, Laravel 5 knows that we are sending JSON encoded string when 
		// controller's response is a php variable, we don't need to use Response::json any more.
		return compact('success', 'data', 'error');
	}
	
	/**
	 * Shorthand for Controller::response(true, ...)
	 *
	 * @see Controller::response
	 */
	public function success($data = array())
	{
		return $this->response(true, $data);
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
