<?php

namespace App\Services\Base;

use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseService
{

    protected function handle(Closure $callback)
    {
        try {
            return $callback();
        } catch (ModelNotFoundException $e) {
            self::throwExceptionJson("Resource Not Found", 404);
        } catch (\Throwable $e) {

            if (config('app.debug', false)) {

                $detailedMessage = sprintf(
                    "Error: %s in %s on line %d",
                    $e->getMessage(),
                    $e->getFile(),
                    $e->getLine(),

                );
                self::throwExceptionJson($detailedMessage, 500);
            }

            self::throwExceptionJson('An unexpected error has occurred.', 500);
        }
    }

    /**
     * Throws an HttpResponseException with a formatted JSON error response.
     * @param mixed $message
     * @param mixed $code
     * @param mixed $errors
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     * @return never
     */
    public function throwExceptionJson($message = 'An error occurred', $code = 500, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        throw new HttpResponseException(response()->json($response, $code));
    }
}
