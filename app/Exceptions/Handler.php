<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        // if ($exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
            
        //     switch ($exception->getStatusCode()) {
        //         // not found
        //         case '404':
        //             return redirect('/admin');
        //             break;
        //         case '419':
        //             return redirect()->back()->withInput()->with('token', csrf_token());
        //             break;
        //     }
        // }
        // else if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
        //     return redirect()->back()->withInput()->with('token', csrf_token());
        // }
        // else{
            return parent::render($request, $exception);
        // }

    }
}
