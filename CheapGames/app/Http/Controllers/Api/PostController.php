<?php

// Importando as classes necessárias
namespace App\Http\Controllers\Api;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Posts;
use App\Models\Images;

class PostController extends Controller
{
    // Função para listar todos os posts
    public function index()
    {
        // Recupera todos os posts com suas respectivas relações
        $posts = Posts::with('category', 'platform', 'images', 'ratings.user')->get();

        // Verifica se os posts foram recuperados corretamente
        if ($posts->isEmpty()) {
            return response()->json([
                'status' => 404,
                'error' => 'Nenhum post encontrado',
            ]);
        }

        // Adiciona likes, dislikes e nomes dos usuários à resposta
        foreach ($posts as $post) {
            $ratingsSum = $post->ratingsSum();
            $post->likes = $ratingsSum['likes'];
            $post->dislikes = $ratingsSum['dislikes'];

            // Adiciona os nomes dos usuários que deram like e dislike
            $post->userLikes = $post->ratings->where('Liked', 1)->pluck('user.Username');
            $post->userDislikes = $post->ratings->where('Liked', 0)->pluck('user.Username');
        }

        // Remove o atributo 'ratings' do post
        $posts->makeHidden('ratings');

        return response()->json([
            'status' => 200,
            'posts' => $posts,
        ]);
    }
    
    // Função para criar um novo post
    public function store(Request $request)
    {
        // Valida os dados recebidos
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
        ]);

        // Verifica se foi enviada uma imagem
        if ($request->input('ImageURL') == null){
            $imgurURL = "https://imgur.com/a/5DKF8aR";
        } else {
            // Faz o upload da imagem para o Imgur
            $client = new Client();
            try {
                $response = $client->POST('https://api.imgur.com/3/image', [
                    'headers' => [
                        'Authorization' => 'Client-ID 19703dbb1c14e1f',
                    ],
                    'multipart' => [
                        [
                            'name' => 'image',
                            'contents' => fopen($request->file('ImageURL')->getPathname(), 'r'),
                        ],
                    ],
                    'http_errors' => true,
                ]);

                $responseBody = $response->getBody()->getContents();
                $imgurData = json_decode($responseBody, true);
                $imgurURL = $imgurData['data']['link'];
            } catch (ClientException $e) {
                $errorResponse = $e->getResponse();
                $errorBody = $errorResponse->getBody()->getContents();
            }
        }

        // Cria um novo post
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
        ]);

        // Se a imagem foi enviada, cria uma nova imagem associada ao post
        if($imgurURL){
            $post->images()->create([
                'ImageURL' => $imgurURL,
                'Active' => true,
                'Date' => now()
            ]);
        }

        return response()->json(['message' => 'Post criado com sucesso!', 'post' => $post]);
    }

    // Função para exibir um post específico
    public function show(string $id)
    {
        // Recupera o post pelo ID com suas respectivas relações
        $post = Posts::with('category', 'platform', 'images', 'ratings.user')->find($id);
    
        // Verifica se o post foi recuperado corretamente
        if (!$post) {
            return response()->json([
                'status' => 404,
                'error' => 'Post não encontrado',
            ]);
        }
    
        // Adiciona likes, dislikes e nomes dos usuários à resposta
        $ratingsSum = $post->ratingsSum();
        $post->likes = $ratingsSum['likes'];
        $post->dislikes = $ratingsSum['dislikes'];
    
        // Adiciona os nomes dos usuários que deram like e dislike
        $post->userLikes = $post->ratings->where('Liked', 1)->pluck('user.Username');
        $post->userDislikes = $post->ratings->where('Liked', 0)->pluck('user.Username');
    
        // Remove o atributo 'ratings' do post
        unset($post->ratings);
    
        return response()->json([
            'status' => 200,
            'post' => $post,
        ]);
    } 
    

  
    // Função para atualizar um post existente
    public function update(Request $request, string $id)
    {   
        // Valida os dados recebidos
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
        ]);

        // Verifica se foi enviada uma imagem
        if ($request->input('ImageURL') == null){
            $imgurURL = "https://imgur.com/a/5DKF8aR";
        } else {
            // Faz o upload da imagem para o Imgur
            $client = new Client();
            try {
                $response = $client->POST('https://api.imgur.com/3/image', [
                    'headers' => [
                        'Authorization' => 'Client-ID 19703dbb1c14e1f',
                    ],
                    'multipart' => [
                        [
                            'name' => 'image',
                            'contents' => fopen($request->file('ImageURL')->getPathname(), 'r'),
                        ],
                    ],
                    'http_errors' => true,
                ]);

                $responseBody = $response->getBody()->getContents();
                $imgurData = json_decode($responseBody, true);
                $imgurURL = $imgurData['data']['link'];
            } catch (ClientException $e) {
                $errorResponse = $e->getResponse();
                $errorBody = $errorResponse->getBody()->getContents();
            }
        }

        // Recupera o post pelo ID
        $post = Posts::find($id);
        if (!$post) {
            return response()->json(['message' => 'Recurso não encontrado'], 404);
        }
        
        // Se o post existir, atualiza os campos relevantes
        if ($post) {
            $post->update([
                'Title' => $request->input('Title'),
                'Description' => $request->input('Description'),
                'Active' => true,
                'Date' => $request->input('Date'),
                'CategoryID' => $request->input('CategoryID'),
                'PlatformID' => $request->input('PlatformID'),
                'NewPrice' => $request->input('NewPrice'),
                'OldPrice' => $request->input('OldPrice'),
                'Link' => $request->input('Link')
            ]);

            // Se a imagem foi enviada, atualiza a imagem associada ao post
            if($imgurURL){
                $post->images()->update([
                    'ImageURL' => $imgurURL
                ]);
            }
            
            return response()->json($post);
        } else {
            return response()->json(['error' => 'Post não encontrado'], 404);
        }
    }

    // Função para deletar um post existente
    public function destroy(string $id)
    {
        // Recupera o post pelo ID
        $post = Posts::find($id);
        if (!$post) {
            return response()->json(['message' => 'Recurso não encontrado'], 404);
        }

        // Se o post estiver ativo, desativa o post
        if ($post->Active == true) {
            $post->update([
                'Active' => false,
            ]);

            return response()->json(['message' => 'Post deletado com sucesso'], 200);
        } else {
            // Se o post estiver inativo, ativa o post
            $post->update([
                'Active' => true,
            ]);

            return response()->json(['message' => 'Post Ativo com sucesso'], 200);
        }
    }
}