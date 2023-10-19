<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

use function Laravel\Prompts\password;

class UserController extends Controller
{
    public function register(Request $request)
    {
        //TODO: Enviar errores de validation al view
        $incomingFields = $request->validate([
            'username' => ['required', 'min:3', 'max:20', Rule::unique('users', 'username')],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        $incomingFields['password'] = bcrypt($incomingFields['password']);

        $user = User::create($incomingFields);
        auth()->login($user);
        return response()->json(["success" => "Succesfully Register"], 201);
    }

    public function login(Request $request)
    {
        $incomingFields = $request->validate([
            'loginusername' => 'required',
            'loginpassword' => 'required'
        ]);
        if (auth()->attempt(['username' => $incomingFields['loginusername'], 'password' => $incomingFields['loginpassword']])) {
            $request->session()->regenerate();
            return response()->json(['success' => "Succesfully Logged in"], 200);
        } else {
            //TODO: acomodar el mensaje de error
            return response()->json(['error' => "Error"], 401);
        }
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['success' => "You'r now logged out"], 200);
    }
}
