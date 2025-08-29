<?php

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

if (! function_exists('isApiRequestValidator')) {
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
