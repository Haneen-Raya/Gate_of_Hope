<?php

use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return function (Exceptions $exceptions) {
    //        لتوحيد شكل الردود كال controller
    $makeErrorResponse = function ($message, $data = null, int $status = 400) {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
        ], $status);
    };
    // ModelNotFoundException
    $exceptions->render(function (ModelNotFoundException $e, $request) use ($makeErrorResponse) {
        return $makeErrorResponse('Resource not found', null, 404);
    });
    // AuthorizationException    => 1
    $exceptions->render(function (AuthorizationException $e, $request) use ($makeErrorResponse) {
        return $makeErrorResponse('This action is unauthorized.', null, 403);
    });
    // AccessDeniedHttpException => 2
    $exceptions->render(function (AccessDeniedHttpException $e, $request) use ($makeErrorResponse) {
        return $makeErrorResponse('This action is unauthorized.', null, 403);
    });
    // MethodNotAllowedHttpException
    $exceptions->render(function (MethodNotAllowedHttpException $e, $request) use ($makeErrorResponse) {
        return $makeErrorResponse('Method not allowed', null, 405);
    });
    // ValidationException
    $exceptions->render(function (ValidationException $e, $request) use ($makeErrorResponse) {
        return $makeErrorResponse('Validation failed', $e->errors(), 422);
    });
    // AuthenticationException
    $exceptions->render(function (AuthenticationException $e, $request) use ($makeErrorResponse) {
        return $makeErrorResponse('Unauthenticated', null, 401);
    });
    // QueryException
    $exceptions->render(function (QueryException $e, $request) use ($makeErrorResponse) {
        return $makeErrorResponse('Database error occurred. Check your inputs or relations.', [
            'error' => $e->getMessage(), // for delete
        ], 422);
    });

    // NotFoundHttpException
    // $exceptions->render(function (NotFoundHttpException $e, $request) use ($makeErrorResponse) {
    //     return $makeErrorResponse('Endpoint not found', null, 404);
    // });
    $exceptions->render(function (NotFoundHttpException $e, $request) use ($makeErrorResponse) {
        if ($e->getPrevious() instanceof ModelNotFoundException) {
            $model = class_basename($e->getPrevious()->getModel());
            return $makeErrorResponse(" $model Resource not found", null, 404);
        }

        return $makeErrorResponse('Endpoint not found', null, 404);
    });

    // ThrottleRequestsException -> Too many requests
    $exceptions->render(function (ThrottleRequestsException $e, $request) use ($makeErrorResponse) {
        return response()->json([
            'success' => false,
            'message' => 'Too many requests. Please try again later.'
        ], 429);
    });



    $exceptions->render(function (\Throwable $e, $request) use ($makeErrorResponse) {
        return $makeErrorResponse('Internal Server Error', [
            'exception' => get_class($e),
            'message'   => $e->getMessage(), // for delete
        ], 500);
    });

};


/**
 *    ModelNotFoundException
 *    AuthorizationException
 *    AccessDeniedHttpException
 *    NotFoundHttpException
 *    MethodNotAllowedHttpException
 *    ValidationException
 *    AuthenticationException
 */
