<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthController extends Controller
{
    //
    public function login(Request $request){
        
        $credentials = $request->only([
            'Email',
            'Password'
        ]);

        $user = User::where('Email', $request->Email)->first();

        //Pode fazer um if pra isso só ocorrer caso a pessoa
        // queira sair de todos aparelhos
        // que ela conectou
        //$user->tokens()->delete();

        
        //Se tiver criptografia na senha usa esse 
        // if(!$user || !Hash::check($request->Password, $user->Password)){
        //     throw ValidationException::withMessages([
        //         'email' => ['As credenciais estão incorretas']
        //     ]);
        // }
        //Se a senha não tem criptografia usa isso
        if(!$user || $request->Password != $user->Password){
            throw ValidationException::withMessages([
                'email' => ['As credenciais estão incorretas']
            ]);
        }
        $token = $user->createToken('invoice')->plainTextToken;
        
        return response()->json([
            'token' => $token
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return $this->response('Token deletado', 200);
    }
}
