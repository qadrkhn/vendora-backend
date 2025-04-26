<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\CategoryCollection;

class CategoryController extends Controller
{
    public function test(Request $request)
    {
        $all_cate = Category::all()->load('files');
        return response()->success(new CategoryCollection($all_cate), 200, 'All files');
    }
}
