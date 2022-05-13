<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $L = Session()->get('L');
        if($L==null){
            $B = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
            $A = substr($B, 0, 2);
            App::setLocale($A);
        }
        else  {
            App::setLocale($L);
        };

        if ($exception instanceof TokenMismatchException){

//            return Redirect::to('login')->with('InfoBox', 'Session expired, please re-login !');
            return Redirect::route('login')->with('InfoBox', 'eMateri membutuhkan login kembali');
        }
        if($this->isHttpException($exception))
        {
            switch ($exception->getStatusCode())
            {
                // not found kode 404
                case 404:
                    return view('desktop/error');
                    break;
                // internal error
                case 421:
                    return view('desktop/error');
                    break;
                // internal error
                case 500:
                    return view('desktop/error');
                    break;
                default:
                    return view('desktop/error');
                    break;
            }
        }

        else {
            return parent::render($request, $exception);
    }
    }
}
