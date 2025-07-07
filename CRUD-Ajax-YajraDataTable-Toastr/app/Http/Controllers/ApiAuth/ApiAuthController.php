<?php

namespace App\Http\Controllers\ApiAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiAuth\LoginRequest;
use App\Http\Requests\ApiAuth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ApiAuthController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        try {
            // $input = $request->all();
            $input = $request->validated();
            $input['password'] = bcrypt($input['password']);

            User::create($input);
            $data = $this->generateAuthToken();

            return successResponse('User registered successfully', $data);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Login api
     */
    public function login(LoginRequest $request)
    {
        try {
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

                $data = $this->generateAuthToken();
                return successResponse('User login successfully', $data);
            } else {
                throw new \Exception('Password or Email is Wrong');
            }
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     *Logout Api
     */
    public function logout()
    {
        try {
            Auth::logout();

            return successResponse('User Logout Successfully', []);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }
}
