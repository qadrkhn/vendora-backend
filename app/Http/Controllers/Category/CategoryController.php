<?php

namespace App\Http\Controllers\Category;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use App\Models\File\File;
use App\Http\Resources\Category\CategoryResource;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Requests\Category\CreateCategoryRequest;

class CategoryController extends Controller
{
    public function store(CreateCategoryRequest $request)
    {
        try {
            DB::beginTransaction();

            $categoryFileId = $request->file[0];
            $categoryFileId = $categoryFileId['id'];

            $file = File::findOrFail($categoryFileId);

            $category = Category::create([
                'url' => $request->url,
                'name' => $request->name,
                'featured' => $request->featured
            ]);

            $file->fileable_type = Category::class;
            $file->fileable_id = $category->id;
            $file->save();

            DB::commit();

            return response()->success(new CategoryResource($category->load('file')), 201, 'Category created successfully');

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->error('Category creation failed.', 500, $e->getMessage());
        }
    }
}
