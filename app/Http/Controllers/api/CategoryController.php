<?php

namespace App\Http\Controllers\api;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\StoreUpdateCategoryRequest;

class CategoryController extends Controller
{

    public function index()
    {
        return Category::paginate(10);
    }

    public function store(StoreUpdateCategoryRequest $request)
    {
        $newCategory = Category::create($request->validated());
        return new CategoryResource($newCategory);
    }

    public function show(Category $category)
    {
        return new CategoryResource($category);
    }

    public function update(StoreUpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        if ($category->transactions->isEmpty()) {
            $category->forceDelete();
        } else {
            $category->delete();
        }

        return new CategoryResource($category);
    }
}
