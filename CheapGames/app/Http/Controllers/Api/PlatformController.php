<?php

// Importando as classes necessárias
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;

class PlatformController extends Controller
{
    // Função para listar todas as plataformas
    public function index()
    {
        $platform = Platform::all(); // Recupera todas as plataformas
        return response()->json(['platform' => $platform]); // Retorna as plataformas como JSON
    }

    // Função para criar uma nova plataforma
    public function store(Request $request)
    {
        $name = strtoupper($request->Name); // Converte o nome da plataforma para maiúsculas
        $existingPlatform = Platform::where('Name', $name)->first(); // Verifica se a plataforma já existe

        if ($existingPlatform) { // Se a plataforma já existir, retorna um erro
            return response()->json(['error' => 'Plataforma já existe'], 400);
        }

        $platform = new Platform; // Cria uma nova plataforma
        $platform->Name = $name; // Define o nome da plataforma
        $platform->save(); // Salva a plataforma no banco de dados

        return response()->json($platform, 201); // Retorna a plataforma criada como JSON
    }

    // Função para exibir uma plataforma específica
    public function show(string $id)
    {
        $platform = Platform::find($id); // Recupera a plataforma pelo ID
        if ($platform) { // Se a plataforma existir, retorna a plataforma como JSON
            return response()->json($platform);
        } else { // Se a plataforma não existir, retorna um erro
            return response()->json(['error' => 'Plataforma não encontrada'], 404);
        }
    }

    // Função para atualizar uma plataforma existente
    public function update(Request $request, string $id)
    {
        $name = strtoupper($request->Name); // Converte o nome da plataforma para maiúsculas
        $existingPlatform = Platform::where('Name', $name)->first(); // Verifica se a plataforma já existe

        if ($existingPlatform && $existingPlatform->PlataformaID != $id) { // Se a plataforma já existir e não for a plataforma que está sendo atualizada, retorna um erro
            return response()->json(['error' => 'Plataforma já existe'], 400);
        }

        $platform = Platform::find($id); // Recupera a plataforma pelo ID
        if ($platform) { // Se a plataforma existir
            $platform->Name = $name; // Atualiza o nome da plataforma
            $platform->save(); // Salva a plataforma no banco de dados

            return response()->json($platform); // Retorna a plataforma atualizada como JSON
        } else { // Se a plataforma não existir, retorna um erro
            return response()->json(['error' => 'Plataforma não encontrada'], 404);
        }
    }

    // Função para deletar uma plataforma existente
    public function destroy(string $id)
    {
        $platform = Platform::find($id); // Recupera a plataforma pelo ID
        if ($platform) { // Se a plataforma existir
            $platform->delete(); // Deleta a plataforma

            return response()->json(['success' => 'Plataforma deletada com sucesso']); // Retorna uma mensagem de sucesso
        } else { // Se a plataforma não existir, retorna um erro
            return response()->json(['error' => 'Plataforma não encontrada'], 404);
        }
    }
}
