<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Http\Resources\UserResource;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
      
        $user = User::where('email', $request->email)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            return response()->json([
               'user' => new UserResource($user),
               'token' => $token,
               'can' => $user->getAllPermissions()->pluck('name'),
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}