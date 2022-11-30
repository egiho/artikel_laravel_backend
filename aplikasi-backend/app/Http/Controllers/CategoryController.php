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
}
