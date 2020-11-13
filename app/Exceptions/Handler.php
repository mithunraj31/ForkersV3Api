<?php

namespace App\Exceptions;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use InvalidArgumentException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
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
        //
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundResourceException
        || $exception instanceof ModelNotFoundException) {
            $message = 'Data not found.';
            if ($exception->getMessage()) {
                $message = $exception->getMessage();
            }
            return response()->json(['message' => $message], 404);
        } else if ($exception instanceof InvalidFormatException) {
            return response()->json(['message' => $exception->getMessage()], 500);
        } else if ($exception instanceof InvalidArgumentException) {
            return response()->json(['message' => $exception->getMessage()], 400);
        } else if ($exception instanceof StonkamResultIsFailedException) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }

        return parent::render($request, $exception);
    }
}
