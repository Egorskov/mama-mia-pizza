<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\SomeThingWentWrongException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        SomeThingWentWrongException::class,
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (SomeThingWentWrongException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'description' => 'mdaaaa',
                'error' => true
            ], 500);
        });
    }

    public function render($request, Throwable $e)
    {

        if ($e instanceof SomeThingWentWrongException) {
            return response()->json([
                'message' => $e->getMessage(),
                'description' => 'mdaaaa',
                'error' => true
            ], 500);
        }

        return parent::render($request, $e);
    }
}
