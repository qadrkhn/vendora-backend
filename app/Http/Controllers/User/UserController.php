<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;

class UserController extends Controller
{
    public function me(Request $request)
    {
        $user = $request->user();
        if($user === null)
        {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        return response()->json(new UserResource($user), 200);
    }
}
