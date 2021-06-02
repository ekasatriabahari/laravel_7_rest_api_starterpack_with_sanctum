<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// calling User model
use \App\User;

class AuthController extends Controller
{
    //Register By Admin controller
    public function registeredByAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $new_user = new \App\User;
        $new_user->name = $request->get('name');
        $new_user->email = $request->get('email');
        $new_user->password = Hash::make($request->get('password'));
        $new_user->role = $request->get('role');

        $user = $request->user();

        if ($user->tokenCan('user:create')) {

            $new_user->save();
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status' => 'failed', 'data' => 'Unauthorised']);
    }

    //Register By User controller
    public function registeredByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $new_user = new \App\User;
        $new_user->name = $request->get('name');
        $new_user->email = $request->get('email');
        $new_user->password = Hash::make($request->get('password'));
        $new_user->role = 'user'; //role is user because user register it self

        $user = $request->user();

        $result = $new_user->save();
        if ($result) {
            return response()->json(['status' => 'success'], 200);
        }

        return response()->json(['status' => 'failed', 'data' => 'Error System'], 500);
    }

    // Login Controller
    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['status' => 'failed', 'data' => 'Wrong password']);
        }

        $abilities = $user->role == 'admin' ? ['user:index', 'user:create'] : ['user:index'];
        return response()->json([
            'status' => 'User login successfully.',
            'data' => $user->createToken($request->device_name, $abilities)->plainTextToken,
        ]);
    }

    // Logout controller
    public function logout()
    {
        $user = request()->user();

        if (request()->token_id) {

            $user->tokens()->where('id', request()->token_id)->delete();
            return response()->json(['status' => 'Successfully logged out']);
        }

        $user->tokens()->delete();
        return response()->json(['status' => 'Successfully logged out']);
    }
}
