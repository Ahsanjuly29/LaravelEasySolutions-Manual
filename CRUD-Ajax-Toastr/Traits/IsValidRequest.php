<?php

namespace App\Traits;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

trait IsValidRequest
{
    public function validationData()
    {
        try {
            // Checking if the request is valid api request or not.
            isApiRequestValidator($this);
            return $this->all();
        } catch (\Exception $e) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 0,
                    'message' => $e->getMessage(),
                ], 422)
            );
        }
    }

    /**
     * Function that rewrites the parent method and throwing
     * custom exceptions of validation.
     */
    public function failedValidation(Validator $validator)
    {
        if ($validator->fails()) {
            throw new HttpResponseException(
                response()->json([
                    'status' => 0,
                    'message' => $validator->getMessageBag()->toArray(),
                    'errors' => $validator->errors(),
                ], 422)
            );
        }
    }
}
