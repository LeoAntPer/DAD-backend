<?php

namespace App\Http\Controllers\api;

use App\Models\DefaultCategory;
use App\Http\Controllers\Controller;
use App\Http\Resources\DefaultCategoryResource;
use App\Http\Requests\StoreUpdateDefaultCategoryRequest;

class DefaultCategoryController extends Controller
{
    public function index()
    {
        return DefaultCategoryResource::collection(DefaultCategory::all());
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

    public function destroy(DefaultCategory $defaultCategory)
    {
        $defaultCategory->delete();
        return new DefaultCategoryResource($defaultCategory);
    }
}
