<?php

// Importando as classes necessárias
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Função para listar todos os usuários
    public function index()
    { 
        $users = User::all(); // Recupera todos os usuários
        return response()->json(['users' => $users]); // Retorna os usuários como JSON
    }

    // Função para criar um novo usuário
    public function store(Request $request)
    {    
        // Validação dos dados da solicitação
        $validatedData = $request->validate([
            'Username' => 'required|max:255',
            'Password' => 'required|min:8',
            'Credit' => 'required|numeric',
            'Date' => 'required|date',
        ]);

        // Obter todos os dados da solicitação
        $userData = $request->all();
        $email = $userData["Email"];
        $cpf = $userData["CPF"];
        
        // Isso criptografa a senha
        $userData["Password"] = bcrypt($userData["Password"]); 

        // Verifica se já existe um usuário com o email fornecido
        $existingUserByEmail = User::where("Email", $email)->first();

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
        $existingUserByCpf = User::where("CPF", $cpf)->first();

        if ($existingUserByCpf) {
            return response()->json(['error' => 'A user with this CPF already exists'], 409);
        }
        // Se nenhum usuário com o email ou CPF fornecidos existir, cria um novo usuário
        try {
            User::create([
                'Username' => $userData["Username"],
                'Email' => $userData["Email"],
                'Password' => $userData["Password"],
                'Credit' => $userData["Credit"],
                'CPF' => $userData["CPF"],
                'Active' => true,
                'Date' => $userData["Date"],
            ]);
            return response()->json(['message' => 'User created successfully'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error creating user'], 500);
        } 
    }

    // Função para exibir um usuário específico
    public function show(string $id)
    {
        return User::findOrFail($id); // Recupera o usuário pelo ID e retorna como JSON
    }

    // Função para atualizar um usuário existente
    public function update(Request $request, string $id)
    {
        $usuario = User::findOrFail($id); // Recupera o usuário pelo ID
        $email = $request->input("Email");

        // Verifica se o email enviado na atualização é diferente do email atual do usuário
        if ($email !== $usuario->email) {
            // Verifica se já existe um usuário com o novo email no banco de dados
            $existingUser = User::where("Email", $email)->first();
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

    // Função para deletar um usuário existente
    public function destroy(string $id)
    {
        $usuario = User::findOrFail($id); // Recupera o usuário pelo ID
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