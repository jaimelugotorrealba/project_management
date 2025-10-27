<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Api\User\UserRequest;
use App\Http\Requests\Api\User\StoreRequest;
use App\Http\Requests\Api\User\UpdateRequest;

class UserController extends Controller
{
    public function getAllUsers(Request $request)
    {
        $perPag = $request->per_pag ?? 10;
        $users = User::paginate($perPag);
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function getUsers(Request $request)
    {
        $users = User::get();
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    public function store(StoreRequest $request)
    {
        if (!$request->authorize()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }
        $validatedData = $request->validated();

        $user = new User();
        $user->email = $validatedData['email'];
        $user->first_name = $validatedData['first_name'];
        $user->last_name = $validatedData['last_name'];
        $user->role = $validatedData['rol'];
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado con éxito',
            'user' => $user
        ], 201);
    }
    public function updateProfile(UserRequest $request)
    {
        $user = $request->user();

        $validated = $request->validated();

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];

        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Perfil actualizado con éxito',
            'profile_image' => $user->profile_image,
            'url_profile_image' => $user->url_profile_image,
        ], 200);
    }

    public function update(UpdateRequest $request, User $user)
    {
        $validatedData = $request->validated();

        if (isset($validatedData['email'])) {
            $user->email = $validatedData['email'];
        }
        if (isset($validatedData['first_name'])) {
            $user->first_name = $validatedData['first_name'];
        }
        if (isset($validatedData['last_name'])) {
            $user->last_name = $validatedData['last_name'];
        }
        if (isset($validatedData['rol'])) {
            $user->role = $validatedData['rol'];
        }
        if (!empty($validatedData['password'])) {
            $user->password = Hash::make($validatedData['password']);
        }
        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }
            $path = $request->file('profile_image')->store('profile_images', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado con éxito',
            'user' => $user
        ], 200);
    }

    public function destroy(User $user)
    {
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Usuario eliminado con éxito'
        ], 200);
    }
}
