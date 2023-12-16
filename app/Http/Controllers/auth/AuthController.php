<?php

namespace App\Http\Controllers\auth;

use App\Models\ViewAuthUser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Resources\ViewAuthUserResource;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = ViewAuthUser::where('username', $request->username)->first();

        $passportData = [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_GRANT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_GRANT_SECRET'),
            'username' => $request->username,
            'password' => $request->password,
            'scope'         => '',
        ];

        request()->request->add($passportData);

        $request = Request::create(env('PASSPORT_URL') . '/oauth/token', 'POST');
        $response = Route::dispatch($request);
        $errorCode = $response->getStatusCode();

        if ($errorCode == '200') {
            if ($user && $user->blocked) {
                return response()->json(
                    ['msg' => 'User is blocked'],
                    $errorCode
                );
            }
            return json_decode((string) $response->content(), true);
        } else {
            return response()->json(
                ['msg' => 'User credentials are invalid'],
                $errorCode
            );
        }
    }

    public function logout(Request $request)
    {
        $accessToken = $request->user()->token();
        $token = $request->user()->tokens->find($accessToken);
        $token->revoke();
        $token->delete();
        return response(['msg' => 'Token revoked'], 200);
    }

    public function show_me(Request $request)
    {
        return new ViewAuthUserResource($request->user());
    }
}
