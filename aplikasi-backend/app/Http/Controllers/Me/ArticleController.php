<?php

namespace App\Http\Controllers\Me;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Me\Article\StoreRequest;
use App\Models\Article;
use Str;
use ImageKit\ImageKit;
use App\Models\User;
use App\Models\Article;
use Error;

class ArticleController extends Controller
{
    public function index()
    {
        // get user id yang saat ini sedang login
        // get article dimana user id nya yang sat ini sedang login
        // get juga category dan usernya siapa
        // buat paginationnya

        $userId = auth()->id();

        $articles = Article::with(['category', 'user:id,name,email,picture,'])->select([
            'id', 'user_id', 'category_id', 'title', 'slug', 'content_preview', 'featured_image', 'created_at', 'updated_at',
        ])

            ->where('user_id', $userId)
            ->paginate();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'message' => 'Articles Fetched Successfully.',
            ],
            'data' => $articles,
        ]);

    }
    public function store(StoreRequest $Request)
    {

        // category_id
        // title
        // content
        // featured_image

        // validasi semua request yang masuk
        // terima di controller
        // lalu generate slug dari title
        // generate content preview dari request content
        // get file image lalu ubah ke base64 untuk dikirim ke imageKit
        // get url imageKit dari image yang kita kirim tadi
        // get useer id login
        // lalu create artcle berdasarkan data yang sudah kita proses dimana usernya adalah user login

        $validated = $request->validated();

        $validated['slug'] = Str::of($validated['title'])->slug('-') . '-' . time();
        $validated['content_preview'] = substr($validated['content'], 0, 218) . '...';

        $imageKit = new ImageKit(
            env('IMAGEKIT_PUBLIC_KEY'),
            env('IMAGEKIT_PRIVATE_KEY'),
            env('IMAGEKIT_URL_ENDPOINT'),
        );

        $image = base64_encode(file_get_contents($request->file('featured_image')));

        $uploadImage = $imageKit->uploadFile([
            'file' => $image,
            'fileName' => $validated['slug'],
            'folder' => '/article',
        ]);

        $validated['featured_image'] = $uploadImage->result->url;

        $userId = auth()->id();

        $createArticle = User::find($userId)->articles()->create($validated);

        if ($createArticle)
        {
            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Article Created Successfully.',
                ],
                'data' => [],
            ]);
        }

        return response()->json([
            'meta' => [
                'code' => 500,
                'status' => 'error',
                'message' => 'Error! Article Failed to Create.',
            ],
            'data' => [],
        ], 500);
    }

    public function show($id)
    {
        // get article berdasarkan id yang diberikan
        // cek apakah berhasil get
        // kalau artikel tidak berhasil di get maka kembalikan response not found
        // jika article berhasil di get id user saat ini login
        // cek apakah id user yg saat ini login sama dengan id user yang ada di article yang kita get
        // jika tidak sama maka kembalikan response unauthorized
        // jika sama kembalikan article success

        $article = Article::with(['category', 'user:id,name,picture'])->find($id);

        if ($article)
        {
            $userId = auth()->id();

            if ($article->user_id === $userId)
            {
                return response()->json([
                    'meta' => [
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Article Fetched Successfully.',
                    ],
                    'data' =>$article,
                ]);
            }

            return response()->json([
                'meta' => [
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Unauthorized.',
                ],
                'data' => [],
            ], 401);
        }

        return response()->json([
            'meta' => [
                'code' => 404,
                'status' => 'error',
                'message' => 'Article Not Found.',
            ],
            'data' => [],
        ], 404);
    }
}
