<?php

// if (!function_exists('getCompanyData')) {
//     function getCompanyData()
//     {
//         return cache()->remember('company_info', 86400, function () {
//             return \App\Models\Company::first();
//         });
//     }
// }

// Custom Success Response
if (! function_exists('successResponse')) {
    function successResponse($message, $data = [])
    {
        return response()->json([
            'success' => 1,
            'message' => $message,
            'data' => $data,
        ], 200);
    }
}

// Custom Message For the Production and other Staff
if (! function_exists('errorResponse')) {
    function errorResponse($e, $message = '', $code = 422)
    {
        // For production this will throw Default Message and
        // For Local and Under Development, This will throw Real Error Messages.
        if (app()->environment('production')) {
            return response()->json([
                'status' => 0,
                'message' => ! empty($message) ? $message : 'Something went wrong ! Failed to complete this action.',
                'data' => [],
            ], $code);
        } else {
            return response()->json([
                'status' => 0,
                'message' => $e->getMessage() . '. Line: ' . $e->getLine() . '. File: ' . $e->getFile(),
                'data' => [],
            ], $code);
        }
    }
}
