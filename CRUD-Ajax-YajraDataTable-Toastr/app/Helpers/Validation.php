<?php

use Illuminate\Http\Exceptions\HttpResponseException;

if (! function_exists('isApiRequestValidator')) {
    /**
     * Function that checkes the api request is valid or not.
     * Globally diclared as because use of this function will be used in all the requests
     * and in the respective functions.
     */
    function isApiRequestValidator($request)
    {
        try {
            $jsonCheck = $request->wantsJson();
            if (! $jsonCheck) {
                throw new Exception('Invalid Request');
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

if (! function_exists('isApiRequest')) {
    /**
     * Function that checkes the api request is valid or not.
     * Globally diclared as because use of this function will be used in all the requests
     * and in the respective functions.
     */
    function isApiRequest($request)
    {
        try {
            $jsonCheck = $request->wantsJson();
            if (! $jsonCheck) {
                throw new Exception('Invalid Request');
            }
        } catch (Exception $e) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 0,
                    'message' => $e->getMessage(),
                ], 401)
            );
        }
    }
}
