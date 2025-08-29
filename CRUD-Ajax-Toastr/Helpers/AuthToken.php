<?php

if (!function_exists('refreshTokenCache')) {
    function refreshTokenCache()
    {
        if (auth()->check()) {
            $user = auth()->user();
            deleteUserToken(); // Delete all tokens with this name
            $token = $user->createToken('api-token')->plainTextToken; // Create new token
            cache()->put('user_token_' . $user->id, $token, 60 * 60 * 24); // 24 hours // Cache it manually

            return $token;
        }
        return null;
    }
}

if (!function_exists('getUserToken')) {
    function getUserToken()
    {
        if (auth()->check()) {

            $token = auth()->user()->tokens()->where('name', 'api-token')->first();
            if ($token) {
                // && !$token->trashed() // if soft deleted
                return cache()->get('user_token_' . auth()->id()) ?? null;
            }
        }

        return null;
    }
}


if (!function_exists('deleteUserToken')) {
    function deleteUserToken()
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Delete all tokens with this name
            $user->tokens()->where('name', 'api-token')->delete();

            // Remove token from cache
            cache()->forget('user_token_' . $user->id);
        }

        return null;
    }
}
