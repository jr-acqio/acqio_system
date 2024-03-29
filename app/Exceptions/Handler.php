<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof TokenMismatchException){
              //redirect to a form. Here is an example of how I handle mine
            return redirect($request->fullUrl())->with(['msg'=>"Opps! Não é possível submeter um formulário por um longo tempo. Por favor, tente novamente!",'class'=>'info']);
        }
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException)
        {
            return \Response::make(['error'=>'not_found','error_message'=>'Please check the URL you submitted'], 404);
        }
        if($e instanceof \ErrorException){
            return $e->getMessage();
        }
        if($e instanceof \Illuminate\Database\QueryException){
          return $e->getMessage();
        }
      return parent::render($request, $e);
    }
}
