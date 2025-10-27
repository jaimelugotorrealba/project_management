<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Api\Authentication\LoginRequest;
use App\Http\Requests\Api\Authentication\RegisterRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'password' => Hash::make($request->password),
            'role' => $request->filled('role') ? $request->role : 'dev'
        ]);

        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
            'success' => true,
            'message' =>'Usuario registrado correctamente',
            'token' => $token,
            'user' => $user
        ],201);
    }
    public function login(LoginRequest $request)
    {
        if(!Auth::attempt($request->only('email','password'))){
            return response()->json(['success'=> false,'message' => 'Credenciales invalidas']);
        }

        $user = Auth::user();
        $token = $user->createToken('api_token')->plainTextToken;
        return response()->json([
            'success' =>true,
            'message' => 'Inicio de sesion correcto',
            'token' => $token,
            'user' => $user
        ]);
    }

    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'success'=>true,
            'message'=>'Sesion cerrada correctamente'
        ]);
    }
}
