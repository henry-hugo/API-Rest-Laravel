<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Posts;
use App\Models\Images;


class PostController extends Controller
{
    
    public function index()
    {
        $posts = Posts::with('category', 'platform', 'images')->get();
        return response()->json(['posts' => $posts]);
    }
    
   
    public function store(Request $request)
    {
        dd($request);
        
        // Validação dos dados recebidos
        $request->validate([
            'UserID' => 'required|integer',
            'Title' => 'required|string|max:255',
            'Description' => 'required|string',
            'Active' => 'required|boolean',
            'Date' => 'required|date',
            'CategoryID' => 'required|integer',
            'PlatformID' => 'required|integer',
            'NewPrice' => 'required|numeric',
            'OldPrice' => 'required|numeric',
            'Link' => 'nullable|url',
            'ImageURL' => 'required|string|max:255',
            // Adicione outras regras de validação conforme necessário
        ]);

        // Crie um novo post
        $post = Posts::create([
            'UserID' => $request->input('UserID'),
            'Title' => $request->input('Title'),
            'Description' => $request->input('Description'),
            'Active' => $request->input('Active'),
            'Date' => $request->input('Date'),
            'CategoryID' => $request->input('CategoryID'),
            'PlatformID' => $request->input('PlatformID'),
            'NewPrice' => $request->input('NewPrice'),
            'OldPrice' => $request->input('OldPrice'),
            'Link' => $request->input('Link')
            // Preencha outros campos conforme necessário
        ]);

        $imagePath = $request->input('ImageURL', 'default_image.jpg'); // Certifique-se de que 'ImageURL' seja uma string
        $post->images()->update([
            'ImageURL' => $imagePath,
            'Active' => $request->input('Active'),
            'Date' => now()
        ]);

        return response()->json(['message' => 'Post criado com sucesso!', 'post' => $post]);
    }

   
    public function show(string $id)
    {
        $post = Posts::with('category', 'platform', 'images')->find($id); // Busca o post pelo ID
        if ($post) {
            return response()->json(['post' => $post]);
        } else {
            return response()->json(['error' => 'Post não encontrado'], 404);
        }
    }
    
    

  
    public function update(Request $request, string $id)
{   
    
    $request->validate([
        'UserID' => 'required|integer',
        'Title' => 'required|string|max:255',
        'Description' => 'required|string',
        'Active' => 'required|boolean',
        'Date' => 'required|date',
        'CategoryID' => 'required|integer',
        'PlatformID' => 'required|integer',
        'NewPrice' => 'required|numeric',
        'OldPrice' => 'required|numeric',
        'Link' => 'nullable|url',
        'ImageURL' => 'required|string|max:255',
        // Adicione outras regras de validação conforme necessário
    ]);

    $post = Posts::find($id);
    if (!$post) {
        return response()->json(['message' => 'Recurso não encontrado'], 404);
    }
    

    if ($post) {
        // Atualize os campos relevantes com base nos dados do $request
        $post->update([
            'Title' => $request->input('Title'),
            'Description' => $request->input('Description'),
            'Active' => $request->input('Active'),
            'Date' => $request->input('Date'),
            'CategoryID' => $request->input('CategoryID'),
            'PlatformID' => $request->input('PlatformID'),
            'NewPrice' => $request->input('NewPrice'),
            'OldPrice' => $request->input('OldPrice'),
            'Link' => $request->input('Link')
            // Preencha outros campos conforme necessário
        ]);

        $imagePath = $request->input('ImageURL', 'default_image.jpg'); // Certifique-se de que 'ImageURL' seja uma string
        $post->images()->update([
            'ImageURL' => $imagePath
        ]);
        
        return response()->json($post);
    } else {
        return response()->json(['error' => 'Post não encontrado'], 404);
    }
    
}

  
    public function destroy(string $id)
    {
        $post = Posts::find($id);
        if (!$post) {
            return response()->json(['message' => 'Recurso não encontrado'], 404);
            }
            if ($post->Active == true) {
                $post->update([
                    'Active' => false,
                    ]);

                    return response()->json(['message' => 'Post deletado com sucesso'], 200);
            }else{
                $post->update([
                    'Active' => true,
                    ]);

                    return response()->json(['message' => 'Post Ativo com sucesso'], 200);
            }
    }
}
