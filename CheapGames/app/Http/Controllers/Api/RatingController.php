<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ratings;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'PostID' => 'required|integer',
            'Liked' => 'required|boolean',
            'UserID' => 'required|integer', // Certifique-se de que o UserID seja obrigatório
        ]);

        // Verifique se o UserID já existe
        $existingRating = Ratings::where('UserID', $request->input('UserID'))
            ->where('PostID', $request->input('PostID'))
            ->first();

        if ($existingRating) {
            return response()->json(['error' => 'Usuário já deu like neste post'], 404);
        }

        $ratings = [
            'PostID' => $request->input('PostID'),
            'UserID' => $request->input('UserID'),
            'Liked' => $request->input('Liked'),
            'Date' => now(),
        ];

        Ratings::create($ratings);

        return response()->json(['message' => 'like criado com sucesso!', 'ratings' => $ratings]);
    }



    public function destroy(string $id)
    {
        $ratings = Ratings::find($id);
        if (!$ratings) {
            return response()->json(['message' => 'Recurso não encontrado'], 404);
            }else{
                $ratings->delete();
                return response()->json(['message' => 'like deletado com sucesso'], 200);
            }
    }
}
