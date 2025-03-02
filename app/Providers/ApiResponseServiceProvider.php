<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;

class ApiResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function (string $message, $data = null, int $code = 200): JsonResponse {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $data ?? [],
            ], $code);
        });

        Response::macro('error', function (string $message, $data = null, int $code = 400): JsonResponse {
            return response()->json([
                'status' => 'error',
                'message' => $message,
                'data' => $data ?? [],
            ], $code);
        });
    }
}
