<?php

namespace App\Http\Controllers;

use App\Models\Vcard;
use Illuminate\Http\Request;
use App\Http\Requests\StoreVcardRequest;
use App\Http\Requests\StoreUpdateVcardRequest;

class VcardController extends Controller
{
    public function index()
    {
        //
    }

    public function store(StoreVcardRequest $request)
    {
        //
    }

    public function show(Vcard $vcard)
    {
        //
    }

    public function update(StoreUpdateVcardRequest $request, Vcard $vcard)
    {
        //
    }

    public function destroy(Vcard $vcard)
    {
        //
    }
}
