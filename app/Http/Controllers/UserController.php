<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    //
    public function register(RegisterUserRequest $registerUserRequest)
    {
        $registerUserRequest["password"] = Hash::make($registerUserRequest->password);
        User::create($registerUserRequest->toArray());
        return $this->sendResponse("User registered", null, 200);
    }

    public function login(Request $request)
    {
        // Validate request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Retrieve the user by email
        $user = User::where('email', $request->email)->first();

        // Check if the user exists and verify the password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('These credentials do not match our records.', null, 401);
        }

        // Generate a token for the user
        $token = $user->createToken('token')->plainTextToken;
        return $this->sendResponse("Token", $token, 200);
    }


    public function logout()
    {
        $user = Auth::user();

        $user->tokens()->delete();

        return $this->sendResponse("Logout Successfully", null, 200);
    }
}
