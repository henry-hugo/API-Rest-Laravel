<?php

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
    
    public function index()
    {
        $posts = Posts::with('category', 'platform', 'images', 'ratings.user')->get();

        // Verificando se os posts foram recuperados corretamente
        if ($posts->isEmpty()) {
            return response()->json([
                'status' => 404,
                'error' => 'Nenhum post encontrado',
            ]);
        }

        // Adicionando likes, dislikes e nomes dos usuários à resposta
        foreach ($posts as $post) {
            $ratingsSum = $post->ratingsSum();
            $post->likes = $ratingsSum['likes'];
            $post->dislikes = $ratingsSum['dislikes'];

            // Adicionando os nomes dos usuários que deram like e dislike
            $post->userLikes = $post->ratings->where('Liked', 1)->pluck('user.Username');
            $post->userDislikes = $post->ratings->where('Liked', 0)->pluck('user.Username');
        }

        // Removendo o atributo 'ratings' do post
        unset($post->ratings);

        return response()->json([
            'status' => 200,
            'posts' => $posts,
        ]);
    }

    
    
    
    
   
    public function store(Request $request)
    {

        //dd($request);
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
            //'ImageURL' => 'required|string|max:255',
            // Adicione outras regras de validação conforme necessário
        ]);

    
        
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
                    dd($errorBody);
                }

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
        if($imgurURL){
            $post->images()->create([
                'ImageURL' => $imgurURL,
                'Active' => true,
                'Date' => now()
            ]);
        }else{
            $imgurURL =('default_image.jpg');
            $post->images()->create([
                'ImageURL' => $imagePath,
                'Active' => true,
                'Date' => now()
            ]);
        }
    
        

        return response()->json(['message' => 'Post criado com sucesso!', 'post' => $post]);
    }

   

    public function show(string $id)
    {
        $post = Posts::with('category', 'platform', 'images', 'ratings.user')->find($id);
    
        if (!$post) {
            return response()->json([
                'status' => 404,
                'error' => 'Post não encontrado',
            ]);
        }
    
        $ratingsSum = $post->ratingsSum();
        $post->likes = $ratingsSum['likes'];
        $post->dislikes = $ratingsSum['dislikes'];
    
        // Adicionando os nomes dos usuários que deram like e dislike
        $post->userLikes = $post->ratings->where('Liked', 1)->pluck('user.Username');
        $post->userDislikes = $post->ratings->where('Liked', 0)->pluck('user.Username');
    
        // Removendo o atributo 'ratings' do post
        unset($post->ratings);
    
        return response()->json([
            'status' => 200,
            'post' => $post,
        ]);
    }
    


    
    
    

  
    public function update(Request $request, string $id)
{   
    dd($request);
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
        //'ImageURL' => 'required|string|max:255',
        // Adicione outras regras de validação conforme necessário
    ]);


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
        dd($errorBody);
    }

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

        if($imgurURL){
            $post->images()->update([
                'ImageURL' => $imgurURL
            ]);
        }else{
            $imgurURL =('default_image.jpg');
            $post->images()->update([
                'ImageURL' => $imagePath
            ]);
        }
        
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
