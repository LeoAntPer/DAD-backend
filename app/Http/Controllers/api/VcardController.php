<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Vcard;
use App\Http\Resources\VcardResource;
use App\Http\Requests\StoreVcardRequest;
use App\Http\Requests\UpdateVcardRequest;

class VcardController extends Controller
{
    public function index()
    {
        return VcardResource::collection(Vcard::whereNull('deleted_at')->get());
    }

    public function store(StoreVcardRequest $request)
    {
        $newVcard = Vcard::create($request->validated());
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
        $vcard->delete();
        return new VcardResource($vcard);
    }
}
