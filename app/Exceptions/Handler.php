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

    // ДОБАВЬТЕ ЭТОТ МЕТОД - он гарантированно сработает
    public function render($request, Throwable $e)
    {
        // Принудительно обрабатываем ваше исключение
        if ($e instanceof SomeThingWentWrongException) {
            return response()->json([
                'message' => $e->getMessage(),
                'description' => 'mdaaaa',
                'error' => true
            ], 500);
        }

        // Для всех других исключений - стандартное поведение
        return parent::render($request, $e);
    }
}
