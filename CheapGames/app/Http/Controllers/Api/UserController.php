<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users;

class UserController extends Controller
{
 
    public function index()
    { 
        $users = Users::all();
        return response()->json(['users' => $users]);
    }

    public function store(Request $request)
   
    {    
       
        // Obter todos os dados da solicitação
        $userData = $request->all();
        $email = $userData["Email"];
        $cpf = $userData["CPF"];

        // Verifica se já existe um usuário com o email fornecido
        $existingUserByEmail = Users::where("Email", $email)->first();

        if ($existingUserByEmail) {
            // Se o usuário com o email fornecido já existir, verifica se está inativo e o ativa
            if (!$existingUserByEmail->Active) {
                $existingUserByEmail->update(["Active" => true]);
                return response()->json(['message' => 'Existing user activated successfully'], 200);
            } else {
                return response()->json(['message' => 'User already exists and is active'], 200);
            }
        }

        // Verifica se já existe um usuário com o CPF fornecido
        $existingUserByCpf = Users::where("CPF", $cpf)->first();

        if ($existingUserByCpf) {
            return response()->json(['error' => 'A user with this CPF already exists'], 409);
        }

        // Se nenhum usuário com o email ou CPF fornecidos existir, cria um novo usuário
        try {
            Users::create($userData);
            return response()->json(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error creating user'], 500);
        } 
    }
    


    public function show(string $id)
    {
        return Users::findOrFail($id);
    }

    public function update(Request $request, string $id)
{
    $usuario = Users::findOrFail($id);
    $email = $request->input("Email");

    // Verifica se o email enviado na atualização é diferente do email atual do usuário
    if ($email !== $usuario->email) {
        // Verifica se já existe um usuário com o novo email no banco de dados
        $existingUser = Users::where("Email", $email)->first();
        if ($existingUser) {
            // Se o usuário com o novo email já existir, não permite a atualização
            return response()->json(['error' => 'A user with this email already exists'], 409);
        }
    }

    // Verifica se está tentando alterar o CPF
    if ($request->filled("CPF")) {
        // Se estiver tentando alterar o CPF, não permite a atualização
        return response()->json(['error' => 'CPF cannot be updated'], 400);
    }

    // Atualiza os outros campos do usuário
    $usuario->fill($request->except("CPF"))->save();
    return response()->json(['message' => 'User updated successfully'], 200);
}


    public function destroy(string $id)
    {
        $usuario = Users::findOrFail($id);
        if ($usuario->role == "admin") {
            return response()->json([
                'error' => 'No se puede eliminar el usuario administrador',
            ], 409);
        } else {
            $usuario->update(['Active' => false]); // Atualiza apenas a coluna 'Active'
            return response()->json([
                'message' => 'Usuario eliminado correctamente',
            ]);
        }
    }
}