<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateCategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::paginate(10);
    }

    public function store(StoreUpdateCategoryRequest $request)
    {
        //
    }

    public function show(Category $category)
    {
        //
    }

    public function update(StoreUpdateCategoryRequest $request, Category $category)
    {
        //
    }

    public function destroy(Category $category)
    {
        //
    }
}
