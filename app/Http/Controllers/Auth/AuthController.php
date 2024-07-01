<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $messages = [
            'email.required' => 'Email không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
        ];

        $validator = Validator::make($request->all(), [
            'email' => [
                'required','string',
                function ($attribute, $value, $fail) {
                    $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
                    if (!preg_match($emailPattern, $value)) {
                        $fail(response()->json(['result' => false, 'message' => 'Email hoặc số điện thoại không hợp lệ'], 400));
                    }
                }],
            'password' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
        $user = User::where('email', $request->email)->first();
        // dd($user);
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                return $this->loginSuccess($user);
            } else {
                return response()->json(['result' => false, 'message' => "Mật khẩu không chính xác"], 401);
            }
        } else {
            return response()->json(['result' => false,'message' => 'Tài khoản không tồn tại'], 401);
        }
    }
    public function loginSuccess($user, $token = null)
    {
        if (!$token) {
            // dd($user);
            $token = $user->createToken('API Token')->plainTextToken;
            // dd($token);
        }
        return response()->json([
            'result' => true,
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'type' => $user->type,
                'phone' => $user->phone,
                'avatar' => $user->avatar,
                'email_verified' => $user->email_verified_at != null,
                'permissions' => $user->getAllPermissions($user->id, $user->type),
            ]
        ]);
    }
}
