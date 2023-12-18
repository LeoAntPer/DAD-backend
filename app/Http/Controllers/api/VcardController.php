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
use App\Http\Requests\UpdateBlockedVcardRequest;
use Illuminate\Support\Facades\Hash;

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
        // Check if the Vcard has transactions
        if ($vcard->transactions()->exists()) {
            // Soft delete if transactions exist
            $vcard->delete();
        } else {
            // Hard delete if no transactions
            Category::where('vcard',$vcard->phone_number)->forceDelete();
            $vcard->forceDelete();
        }
    }
    public function update_password(UpdateVCardPasswordRequest $request, VCard $vcard)
    {
        $vcard->password = bcrypt($request->validated()['password']);
        $vcard->save();
        return new VCardResource($vcard);
    }

    public function update_confirmation_code(Request $request, VCard $vcard)
    {
        $request->validate([
          'code' => ['required', 'confirmed', 'size:3'],
        ]);
        
        $receivedCode = $request->input('current_code');
        echo($receivedCode);
        if(!Hash::check($receivedCode, $vcard->confirmation_code)) {
            
            abort(422, 'Invalid current code');
        }
        $codeInDB = $validated['code'];
        $vcard->confirmation_code = bcrypt($codeInDB);
        $vcard->save();
        return new VCardResource($vcard);
    }

    public function update_blocked(UpdateBlockedVcardRequest $request, VCard $vcard) {
        $vcard->blocked = $request->validated()['blocked'];
        $vcard->save();
        return new VCardResource($vcard);
    }

    public function destroyWithCredentials(Request $request, Vcard $vcard)
    {
        $rPassword = $request->input('password');
        $rCode = $request->input('code');
        echo("Password\n");
        echo($rPassword);
        echo("\nCode\n");
        echo($rCode);

        // Validate the request data
        $request->validate([
            'password' => 'required',
            'code' => 'required',
        ]);

        

        // Check if the provided password and code are valid
        if(!Hash::check($rPassword, $vcard->password)) {
            abort(422, 'Invalid Password');
        }
        if(!Hash::check($rCode, $vcard->confirmation_code)) {
            abort(422, 'Invalid Code');
        }

        // Check if the Vcard has transactions
        if ($vcard->transactions()->exists()) {
            // Soft delete if transactions exist
            $vcard->delete();
        } else {
            // Hard delete if no transactions
            $vcard->forceDelete();
        }

        // Additional logic or response as needed
        return response()->json(['message' => 'Vcard deleted successfully']);
    }
}
