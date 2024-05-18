<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Platform;

class PlatformController extends Controller
{
    public function index()
    {
        $platform = Platform::all();
        return response()->json(['platform' => $platform]);
    }

    public function store(Request $request)
    {
        $name = strtoupper($request->Name);
        $existingPlatform = Platform::where('Name', $name)->first();

        if ($existingPlatform) {
            return response()->json(['error' => 'Plataforma já existe'], 400);
        }

        $platform = new Platform;
        $platform->Name = $name;
        $platform->save();

        return response()->json($platform, 201);
    }

    public function show(string $id)
    {
        $platform = Platform::find($id);
        if ($platform) {
            return response()->json($platform);
        } else {
            return response()->json(['error' => 'Plataforma não encontrada'], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        $name = strtoupper($request->Name);
        $existingPlatform = Platform::where('Name', $name)->first();
    
        if ($existingPlatform && $existingPlatform->PlataformaID != $id) {
            return response()->json(['error' => 'Plataforma já existe'], 400);
        }
    
        $platform = Platform::find($id);
        if ($platform) {
            $platform->Name = $name;
            $platform->save();
    
            return response()->json($platform);
        } else {
            return response()->json(['error' => 'Plataforma não encontrada'], 404);
        }
    }
    

    public function destroy(string $id)
    {
        $platform = Platform::find($id);
        if ($platform) {
            $platform->delete();

            return response()->json(['success' => 'Plataforma deletada com sucesso']);
        } else {
            return response()->json(['error' => 'Plataforma não encontrada'], 404);
        }
    }
}
