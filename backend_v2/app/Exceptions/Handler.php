<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
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
        // Return JSON errors for external API requests (/api/v1/*)
        if ($request->is('api/v1/*') || $request->is('api/v1')) {
            return $this->renderApiException($request, $exception);
        }

        if ($exception instanceof PostTooLargeException) {
            return back()->with('error', 'The uploaded file is too large. Maximum size is 30MB.');
        }

        return parent::render($request, $exception);
    }

    /**
     * Render a standardized JSON error for external API routes.
     */
    protected function renderApiException($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'code' => 'VALIDATION_ERROR',
                'errors' => $exception->errors(),
            ], 422);
        }

        if ($exception instanceof ModelNotFoundException || $exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => 'error',
                'message' => 'The requested resource was not found.',
                'code' => 'NOT_FOUND',
            ], 404);
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            return response()->json([
                'status' => 'error',
                'message' => 'HTTP method not allowed for this endpoint.',
                'code' => 'METHOD_NOT_ALLOWED',
            ], 405);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Too many requests. Please slow down.',
                'code' => 'RATE_LIMIT_EXCEEDED',
            ], 429);
        }

        if ($exception instanceof PostTooLargeException) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request payload too large.',
                'code' => 'PAYLOAD_TOO_LARGE',
            ], 413);
        }

        // Generic server error (hide details in production)
        $message = config('app.debug')
            ? $exception->getMessage()
            : 'An unexpected error occurred. Please try again later.';

        return response()->json([
            'status' => 'error',
            'message' => $message,
            'code' => 'SERVER_ERROR',
        ], 500);
    }
}
