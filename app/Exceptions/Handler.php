<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        if (($request->ajax() || env('APP_TYPE') == 'api') && !$request->debug) {
            $status = $exception->getCode();
            $message = config("errors.$status");
            $message = !empty($message) ? $message : $exception->getMessage();
            $message = !empty($message) ? $message : 'Unknown error occurred in handler';
            $status = !empty($status) ? $status : 500;
            return response(json_encode([
                'messages' => [
                    'code' => 999,
                    'message' => $message
                ]
            ]), $status)
                ->header('Content-Type', 'text/json');
        }
        return parent::render($request, $exception);
    }
}
