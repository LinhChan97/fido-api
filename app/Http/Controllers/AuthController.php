<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Library\MyValidation;
use App\User;
use App\Http\Resources\MyResource;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), MyValidation::$rulesUser, MyValidation::$messageUser);

        if ($validator->fails()) {
            $message = $validator->messages()->getMessages();
            return response()->json(['message'=>$message, 'status_code' => 202], 202);
        }
        $data = $request->all();
        $user = User::create($data);
        $user->password = $data['password'];

        $model_name = $user->usable_type;
        $role = $model_name::create($data);
        $user->usable_id = $role->id;
        if ($model_name == 'App\\Doctor') {
            $role->actived = 0;
            $role->doctor_no = 'BS' . $role->id;
            $role->email = $user->email;
            $role->save();
        }
        $user->save();

        $token = auth()->login($user);
        return $this->respondWithToken($token, $user);
    }

    public static function responseWithUser($user)
    {
        $role = $user->usable;
        if ($role) {
            return new MyResource($role);
        }
    }

    public function login(Request $request)
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'status_code' => 202,
                'message' => 'Email or password is incorrect'
            ], 202);
        }
        $user = User::where('email', $request['email'])->first();
        return $this->respondWithToken($token, $user);
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['status_code' => 200]);
        } catch (\Exception $ex) {
            return response()->json(['message' => 'Token has been expired'], 404);
        }
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'status_code' => 200,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60,
            'data' => AuthController::responseWithUser($user),
            'usable_type' => $user->usable_type,
            'usable_id' => $user->usable_id,
        ], 200);
    }
}
