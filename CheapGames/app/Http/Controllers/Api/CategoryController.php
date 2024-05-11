<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
   
    public function index()
    {
        return Category::all();
    }
    
    public function store(Request $request)
    {
        $name = strtoupper($request->Name);
        $existingCategory = Category::where('Name', $name)->first();
    
        if ($existingCategory) {
            return response()->json(['error' => 'Categoria já existe'], 400);
        }
    
        $category = new Category;
        $category->Name = $name;
        $category->save();
    
        return response()->json($category, 201);
    }
    
    public function show(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json($category);
        } else {
            return response()->json(['error' => 'Categoria não encontrada'], 404);
        }
    }
    
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->Name = $request->Name;
            $category->save();
    
            return response()->json($category);
        } else {
            return response()->json(['error' => 'Categoria não encontrada'], 404);
        }
    }
    
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
    
            return response()->json(['success' => 'Categoria deletada com sucesso']);
        } else {
            return response()->json(['error' => 'Categoria não encontrada'], 404);
        }
    }
    
}
