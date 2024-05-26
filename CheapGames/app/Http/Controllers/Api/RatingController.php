<?php

// Importando as classes necessárias
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ratings;

class RatingController extends Controller
{
    // Função para listar todas as avaliações
    public function index()
    {
        $ratings = Ratings::all(); // Recupera todas as avaliações
        return response()->json($ratings); // Retorna as avaliações como JSON
    }

    // Função para exibir uma avaliação específica
    public function show(string $id)
    {
        $ratings = Ratings::find($id); // Recupera a avaliação pelo ID
        if ($ratings) { // Se a avaliação existir, retorna a avaliação como JSON
            return response()->json($ratings);
        } else { // Se a avaliação não existir, retorna um erro
            return response()->json(['error' => 'Reação não encontrada'], 404);
        }
    }
    
    // Função para criar uma nova avaliação
    public function store(Request $request)
    {
        // Valida os dados recebidos
        $request->validate([
            'PostID' => 'required|integer',
            'Liked' => 'required|in:true,false,yes,no,1,0', // Aceita 'true', 'false', 'yes', 'no', 1 (true), 0 (false)
            'UserID' => 'required|integer',
        ]);
    
        // Converte 'yes' para true, 'no' para false, '1' para true e '0' para false
        $likedInput = $request->input('Liked');
        if ($likedInput === 'yes' || $likedInput === '1' || $likedInput === 'true') {
            $likedInput = true;
        } elseif ($likedInput === 'no' || $likedInput === '0' || $likedInput === 'false') {
            $likedInput = false;
        }
    
        // Verifica se a avaliação já existe
        $existingRating = Ratings::where('UserID', $request->input('UserID'))
            ->where('PostID', $request->input('PostID'))
            ->first();
    
        if ($existingRating) { // Se a avaliação já existir
            if ($existingRating->Liked == $likedInput) { // Se o novo valor for o mesmo que o valor atual, exclui a avaliação
                $existingRating->delete();
                return response()->json(['message' => 'Avaliação excluída com sucesso.']);
            } else { // Se o novo valor for diferente do valor atual, atualiza a avaliação
                $existingRating->Liked = $likedInput;
                $existingRating->save();
    
                if ($existingRating->Liked) {
                    return response()->json(['message' => 'Like salvo com sucesso.']);
                } else {
                    return response()->json(['message' => 'Dislike salvo com sucesso.']);
                }
            }
        } else { // Se não houver avaliação existente, cria uma nova
            $ratings = [
                'PostID' => $request->input('PostID'),
                'UserID' => $request->input('UserID'),
                'Liked' => $likedInput,
                'Date' => now(),
            ];
    
            Ratings::create($ratings);
    
            return response()->json(['message' => 'Avaliação criada com sucesso!', 'ratings' => $ratings]);
        }
    }
}
