<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }




    public function getDiscountedGames()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://api.steamdb.info/sales/');
        $data = json_decode($response->getBody(), true);

        // Filtrar para apenas jogos em destaque com desconto
        $discounted_games = array_filter($data, function ($game) {
            return $game['is_featured'] && $game['discount'] > 0;
        });

        // Limitar a lista aos 10 primeiros jogos
        $discounted_games = array_slice($discounted_games, 0, 10);

        return response()->json($discounted_games);
    }
}
