<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Users;

class AuthController extends Controller
{
    //
    public function auth(Request $request){

        $credentials = $request->only([
            'Email',
            'Password',
            'device_name'
        ]);

        $user = Users::where('Email', $request->Email)->first();

        //Pode fazer um if pra isso só ocorrer caso a pessoa
        // queira sair de todos aparelhos
        // que ela conectou
        //$user->tokens()->delete();


        if(!$user || !Hash::check($request->Password, $user->Password)){
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas']
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;
        
        return response()->json([
            'token' => $token
        ]);
    }
}
