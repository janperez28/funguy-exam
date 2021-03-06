<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;


class Handler extends ExceptionHandler
{
	protected $defaultUnknownStatusCode = 400;

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
	 * TODO
	 * We need to make sure we are returning the correct HTTP status code based on the current operation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {				
		if ($request->is('api/*'))
		{
			return $this->handleAPIException($request, $e);
		}
	
		return parent::render($request, $e);
    }
	
	/**
	 * Handle exceptions thrown on the API.
	 *
	 * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
	 * @return @return \Illuminate\Http\Response
	 */
	public function handleAPIException($request, Exception $e)
	{
		if ($e instanceof HttpException)
		{
			$statusCode = $e->getStatusCode();
		}
		// If a ModelNotFoundException is thrown, return a Not Found response.
		// The code need to make sure that this is the appropriate exception they need to throw.
		else if ($e instanceof ModelNotFoundException) 
		{
			$statusCode = 404;
		}
		// Return internal server error for fatal errors.
		else if ($e instanceof FatalErrorException)
		{
			$statusCode = 500;
		}
		else 
		{
			$statusCode = $this->defaultUnknownStatusCode;
		}	
		
		// Any other exception, return 400 status code.
		// Make sure we are returning JSON response.		
		// TODO
		// Add support for sending appropriate response type based on Accept header of the request.
		return response()->json(null)->setStatusCode($statusCode);
	}
}
