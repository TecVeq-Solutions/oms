<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, $request) {
            if (!$request->expectsJson()) {
                // Skip for validation and authentication exceptions
                if ($e instanceof \Illuminate\Validation\ValidationException || $e instanceof \Illuminate\Auth\AuthenticationException) {
                    return null; // Let Laravel handle these normally
                }

                $status = $this->isHttpException($e) ? $e->getStatusCode() : 500;
                
                $message = $e->getMessage();
                if (empty($message) || !app()->hasDebugModeEnabled()) {
                    $message = match($status) {
                        401 => 'Unauthenticated. Please log in.',
                        403 => 'Unauthorized Access. You do not have permission to view this page.',
                        404 => 'Looks like you\'re lost. Page not found.',
                        419 => 'Page Expired. Please refresh and try again.',
                        429 => 'Too Many Requests. Please slow down.',
                        500 => 'Internal Server Error. Something went wrong.',
                        503 => 'Service Unavailable. We are doing some maintenance.',
                        default => 'An unexpected error occurred.',
                    };
                }
                
                // Return our custom error view for all other errors
                return response()->view('errors.custom', [
                    'status' => $status,
                    'error_message' => $message
                ], $status);
            }
        });
    }
}
