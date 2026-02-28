<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class BaseApiController extends Controller
{
    /**
     * Return a standardized success response.
     */
    protected function success($data = null, string $message = null, int $code = 200)
    {
        $response = [
            'status' => 'success',
        ];

        if ($message) {
            $response['message'] = $message;
        }

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a standardized error response.
     */
    protected function error(string $message, string $errorCode = 'ERROR', int $httpCode = 400, $errors = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message,
            'code' => $errorCode,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $httpCode);
    }

    /**
     * Return a standardized paginated response.
     */
    protected function paginated($paginator, $resourceClass, string $dataKey = 'data')
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                $dataKey => $resourceClass::collection($paginator->items()),
            ],
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'last' => $paginator->url($paginator->lastPage()),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
            ],
        ], 200);
    }
}
