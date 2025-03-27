<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        if ($exception instanceof TokenMismatchException) {
            return response()->json(['error' => 'CSRF token uyuşmazlığı. Lütfen sayfayı yenileyin ve tekrar deneyin.'], 419);
        }

        if ($exception instanceof HttpException && $exception->getStatusCode() === 419) {
            return response()->json(['error' => 'CSRF token uyuşmazlığı. Lütfen sayfayı yenileyin ve tekrar deneyin.'], 419);
        }

        return parent::render($request, $exception);
    }
}