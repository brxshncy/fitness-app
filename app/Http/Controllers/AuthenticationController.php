<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function loginAsAdmin(LoginRequest $request)
    {
     
        $user = User::where("email", $request->email)->first();

        $isPasswordMatch = Hash::check($request->password, $user->password);

        if (!$isPasswordMatch) {
              return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        // Check if the user actually has the admin role (Spatie)
        if (!$user->hasRole(Role::ADMIN)) {
            return response()->json(['message' => 'Access denied. Not an admin.'], 403);
        }

        $user->tokens()->delete();

        $user->createToken('admin');

        return response()->json([
            'user' => $user, 
            'accessToken' => $user->createToken('admin')->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
