<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;

class CategoryController extends Controller
{
    public function test(Request $request)
    {
        $all_cate = Category::all();
        return response()->json(['message' => $all_cate]);
    }
}
