<?php

namespace App\Exceptions;

use App\Helpers\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;
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
    }

    public function render($request, Throwable $e)
    {
        #dd($e);
        if ($e instanceof ValidationException) {
            return $this->responseError($e->errors(),$e->status);
        }
        if ($e instanceof AuthenticationException){
            return $this->responseError($e->getMessage(),401);
        }
        if ($e instanceof ModelNotFoundException){
            return $this->responseError("Item not found -_-",404);
        }
        if ($e instanceof MethodNotAllowedHttpException){
            return $this->responseError($e->getMessage(),405);
        }
        if ($e instanceof UnAuthorizedException){
            return $this->responseError($e->getMessage(),403);
        }
        return $this->responseError($e->getMessage(),500);
    }
}
