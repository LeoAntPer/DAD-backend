<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Vcard;
use App\Http\Resources\VcardResource;
use App\Http\Requests\StoreVcardRequest;
use App\Http\Requests\UpdateVcardRequest;
use App\Http\Requests\UpdateVCardPasswordRequest;
use App\Models\Category;
use App\Models\DefaultCategory;

use Illuminate\Http\Request;

class VcardController extends Controller
{
    public function index()
    {
        return VcardResource::collection(Vcard::whereNull('deleted_at')->get());
    }

    public function store(StoreVcardRequest $request)
    {
        $newVcard = Vcard::create($request->validated());
        $defaultCategories = DefaultCategory::all();
        foreach ($defaultCategories as $category)
        {
            $newCat = new Category();
            $newCat['vcard']   = $request->phone_number;
            $newCat['type']   = $category->type;
            $newCat['name']   = $category->name;
            $newCat->save();
        }
        return new VcardResource($newVcard);
    }

    public function show(Vcard $vcard)
    {
        return new VcardResource($vcard);
    }

    public function update(UpdateVcardRequest $request, Vcard $vcard)
    {
        $vcard->update($request->validated());
        return new VcardResource($vcard);
    }

    public function destroy(Vcard $vcard)
    {
        $vcard->update(['blocked' => 1]);
        $vcard->delete();
        return new VcardResource($vcard);
    }
    public function update_password(UpdateVCardPasswordRequest $request, VCard $vcard)
    {
        $vcard->password = bcrypt($request->validated()['password']);
        $vcard->save();
        return new VCardResource($vcard);
    }

    public function update_confirmation_code(Request $request, VCard $vcard)
    {
        $validated = $request->validate([
          'code' => ['required', 'confirmed', 'size:3'],
          'current_code' => 'required|in:'.$vcard->confirmation_code
        ]);
        $codeInDB = $validated['code'];
        $vcard->confirmation_code = bcrypt($codeInDB);
        $vcard->save();
        return new VCardResource($vcard);
    }
}
