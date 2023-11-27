<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUpdateUserRequest;

class UserController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function store(StoreUpdateUserRequest $request)
    {
        $newUser = User::create($request->validated());
        return new UserResource($newUser);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(StoreUpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return new UserResource($user);
    }
}
