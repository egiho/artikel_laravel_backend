<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        // get semua category
        // return response json semua category

        $categories = Category::all();

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'Success',
                'message' => 'Categories Fetched Successfully.',
            ],
            'data' => $categories,
        ]);
    }

    public function show($categorySlug)
    {
        // get category dimana slugnya sama dengan categorySlug yg didapat dari route
        // cek apakah category tersebut ada
        // jika ada maka cari category berdasarkan id dari slug category tadi lalu cari article nya
        // return article tersebut
        // (jika kode ini dieksekusi maka artinya category yg dicari tidak ada) kembalikan response error 404, category not found

        $category = Category::where('slug', $categorySlug)->first();

        if ($category)
        {
            $articles = Category::find($category->id)
                ->articles()
                ->with(['category', 'user:id,name,picture'])
                ->select([
                    'id', 'user_id', 'category_id', 'title', 'slug', 'content_preview', 'featured_image', 'created_at', 'updated_at'
                ])
                ->paginate()
            ;

            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'Success',
                    'message' => 'Articles Fetched Successfully.',
                ],
                'data' => $articles,
            ]);
        }

        return response()->json([
            'meta' => [
                'code' => 404,
                'status' => 'error',
                'message' => 'Category Not Found.',
            ],
            'data' => [],
        ], 404);
    }
}
