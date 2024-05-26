<?php

// Importando as classes necessárias
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // Função para listar todas as categorias
    public function index()
    {
        $category = Category::all(); // Recupera todas as categorias
        return response()->json(['category' => $category]); // Retorna as categorias como JSON
    }
    
    // Função para criar uma nova categoria
    public function store(Request $request)
    {
        $name = strtoupper($request->Name); // Converte o nome da categoria para maiúsculas
        $existingCategory = Category::where('Name', $name)->first(); // Verifica se a categoria já existe
    
        if ($existingCategory) { // Se a categoria já existir, retorna um erro
            return response()->json(['error' => 'Categoria já existe'], 400);
        }
    
        $category = new Category; // Cria uma nova categoria
        $category->Name = $name; // Define o nome da categoria
        $category->save(); // Salva a categoria no banco de dados
    
        return response()->json($category, 201); // Retorna a categoria criada como JSON
    }
    
    // Função para exibir uma categoria específica
    public function show(string $id)
    {
        $category = Category::find($id); // Recupera a categoria pelo ID
        if ($category) { // Se a categoria existir, retorna a categoria como JSON
            return response()->json($category);
        } else { // Se a categoria não existir, retorna um erro
            return response()->json(['error' => 'Categoria não encontrada'], 404);
        }
    }
    
    // Função para atualizar uma categoria existente
    public function update(Request $request, string $id)
    {
        $name = strtoupper($request->Name); // Converte o nome da categoria para maiúsculas
        $existingCategory = Category::where('Name', $name)->first(); // Verifica se a categoria já existe

        if ($existingCategory && $existingCategory->CategoriaID != $id) { // Se a categoria já existir e não for a categoria que está sendo atualizada, retorna um erro
            return response()->json(['error' => 'Categoria já existe'], 400);
        }

        $category = Category::find($id); // Recupera a categoria pelo ID
        if ($category) { // Se a categoria existir
            $category->Name = $name; // Atualiza o nome da categoria
            $category->save(); // Salva a categoria no banco de dados

            return response()->json($category); // Retorna a categoria atualizada como JSON
        } else { // Se a categoria não existir, retorna um erro
            return response()->json(['error' => 'Categoria não encontrada'], 404);
        }
    }

    // Função para deletar uma categoria existente
    public function destroy(string $id)
    {
        $category = Category::find($id); // Recupera a categoria pelo ID
        if ($category) { // Se a categoria existir
            $category->delete(); // Deleta a categoria

            return response()->json(['success' => 'Categoria deletada com sucesso']); // Retorna uma mensagem de sucesso
        } else { // Se a categoria não existir, retorna um erro
            return response()->json(['error' => 'Categoria não encontrada'], 404);
        }
    }
}
