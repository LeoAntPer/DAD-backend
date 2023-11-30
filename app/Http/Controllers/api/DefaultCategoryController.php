<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DefaultCategory;
use App\Http\Resources\DefaultCategoryResource;
use App\Http\Requests\StoreUpdateDefaultCategoryRequest;

class DefaultCategoryController extends Controller
{
    public function index()
    {
        return DefaultCategory::paginate(10);
    }

    public function store(StoreUpdateDefaultCategoryRequest $request)
    {
        $newDefaultCategory = DefaultCategory::create($request->validated());
        return new DefaultCategoryResource($newDefaultCategory);
    }

    public function show(DefaultCategory $defaultCategory)
    {
        return new DefaultCategoryResource($defaultCategory);
    }

    public function update(StoreUpdateDefaultCategoryRequest $request, DefaultCategory $defaultCategory)
    {
        $defaultCategory->update($request->validated());
        return new DefaultCategoryResource($defaultCategory);
    }

    public function destroy(DefaultCategory $category)
    {
        $category->delete();
        return new DefaultCategoryResource($category);
    }
}
