<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Use API middleware group
Route::group(array('middleware' => 'api'), function()
{
	// Register resource controller for users.
	// Partial resource for now since we can only add and delete users.
	Route::resource('user', 'UserController', array(
		'only' => array('store', 'destroy')
	));

	Route::group(array('prefix' => 'list'), function()
	{			
		Route::get('nationalities', array('uses' => 'ListController@nationalities'));		
	});
});
