<?php

namespace App\Exceptions;

use Carbon\Exceptions\InvalidFormatException;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use KeycloakGuard\Exceptions\TokenException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\AlreadyUsedException;
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
        if (
            $exception instanceof NotFoundResourceException
            || $exception instanceof ModelNotFoundException
        ) {
            return response()->json(['message' => 'Data not found.'], 404);
        }
        if ($exception instanceof InvalidFormatException) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
        if ($exception instanceof InvalidArgumentException) {
            return response()->json(['message' => $exception->getMessage()], 400);
        }
        if ($exception instanceof StonkamResultIsFailedException) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
        if ($exception instanceof AlreadyUsedException) {
            return response()->json(['message' => 'Id Already Used'], 400);
        }
        if ($exception instanceof TokenException && $request->wantsJson()) {
            return response()->json(['message' => 'Token Expired'], 401);
        }
        if ($exception instanceof NotFoundHttpException && $request->wantsJson()) {
            return response()->json(['message' => 'Url Not Found!'], 404);
        }
        if ($exception instanceof AuthorizationException && $request->wantsJson()) {
            return response()->json(['message' => 'This action is unauthorized!'], 403);
        }
        if ($exception instanceof StonkamInvalidRequestException && $request->wantsJson()) {
            return response()->json(['message' => 'stonkam->' . $exception->getMessage()], 400);
        }
        return parent::render($request, $exception);
    }
}
