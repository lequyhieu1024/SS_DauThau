<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function notYetAuthenticated()
    {
        return response()->json(['message' => 'Vui lòng đăng nhập để tiếp tục.'], 401);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"Auth"},
     *     summary="Login",
     *     operationId="login",
     *     @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *     required={"email", "password"},
     *     @OA\Property(property="email", type="string", format="email", example="lequyhieu1024@gmail.com"),
     *     @OA\Property(property="password", type="string", format="password", example="123456"),
     *     )
     *    ),
     *     @OA\Response(
     *     response=200,
     *     description="Login successfully",
     *     @OA\JsonContent(
     *     @OA\Property(property="result", type="boolean", example=true),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9"),
     *     @OA\Property(property="refresh_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9"),
     *     )
     *    )
     *   ),
     *  )
     */
    public function login(Request $request)
    {
        $messages = [
            'taxcode.required' => 'Mã số thuế không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
            'email.required' => 'Email không được để trống',
        ];

        // Xác thực yêu cầu
        $validator = Validator::make($request->all(), [
            'taxcode' => [
                'required_without:email',
                function ($attribute, $value, $fail) {
                    $taxcodePattern = '/^\d{10}(\d{3})?$/';
                    if (!preg_match($taxcodePattern, $value)) {
                        $fail('Mã số thuế không đúng định dạng');
                    }
                }
            ],
            'email' => [
                'required_without:taxcode',
                'string',
                function ($attribute, $value, $fail) {
                    $emailPattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
                    if (!preg_match($emailPattern, $value)) {
                        $fail('Email không đúng định dạng');
                    }
                }
            ],
            'password' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // Lấy thông tin xác thực
        $credentials = $request->only('taxcode', 'email', 'password');
        $credentials = array_filter($credentials); // Xóa các trường rỗng

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    ['result' => false, 'message' => 'Tài khoản hoặc mật khẩu không chính xác'],
                    401
                );
            }
        } catch (JWTException $e) {
            return response()->json(['result' => false, 'message' => 'Không thể tạo token'], 500);
        }

        return $this->respondWithToken($token);
    }


    //     public function login(Request $request)
    // {
    //     $credentials = $request->only('email', 'password');

    //     if ($token = JWTAuth::attempt($credentials)) {
    //         return $this->respondWithToken($token);
    //     }

    //     return response()->json(['error' => 'Unauthorized'], 401);
    // }

    protected function respondWithToken($token)
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                //            'token_type' => 'bearer',
                //            'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'refresh_token' => JWTAuth::fromUser(auth()->user(), ['refresh' => true])
            ]
        ]);
    }

    public function profile()
    {
        $user = JWTAuth::user();
        return response()->json([
            'result' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'taxcode' => $user->taxcode,
                'email' => $user->email,
                'email_verified' => $user->email_verified_at != null,
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            ]
        ]);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['result' => true, 'message' => 'Đăng xuất thành công']);
        } catch (JWTException $e) {
            return response()->json(['result' => false, 'message' => 'Không thể đăng xuất'], 500);
        }
    }




    // public function signup(Request $request)
    // {
    //     $messages = array(
    //         'name.required' => 'Name is required',
    //         'email_or_phone.required' => $request->register_by == 'email' ? 'Email is required' : 'Phone is required',
    //         'email_or_phone.email' => 'Email must be a valid email address',
    //         'email_or_phone.numeric' => 'Phone must be a number.',
    //         'email_or_phone.unique' => $request->register_by == 'email' ? 'The email has already been taken' : 'The phone has already been taken',
    //         'password.required' => 'Password is required',
    //         'password.confirmed' => 'Password confirmation does not match',
    //         'password.min' => 'Minimum 6 digits required for password'
    //     );
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'password' => 'required|min:6|confirmed',
    //         'email_or_phone' => [
    //             'required',
    //             Rule::when($request->register_by === 'email', ['email', 'unique:users,email']),
    //             Rule::when($request->register_by === 'phone', ['numeric', 'unique:users,phone']),
    //         ],
    //         'g-recaptcha-response' => [
    //             Rule::when(get_setting('google_recaptcha') == 1, ['required', new Recaptcha()], ['sometimes'])
    //         ]
    //     ], $messages);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => $validator->errors()->all()
    //         ]);
    //     }

    //     $user = new User();
    //     $user->name = $request->name;
    //     if ($request->register_by == 'email') {

    //         $user->email = $request->email_or_phone;
    //     }
    //     if ($request->register_by == 'phone') {
    //         $user->phone = $request->email_or_phone;
    //     }
    //     $user->password = bcrypt($request->password);
    //     $user->verification_code = rand(100000, 999999);
    //     $user->save();


    //     // $user->email_verified_at = null;
    //     // if ($user->email != null) {
    //     //     if (BusinessSetting::where('type', 'email_verification')->first()->value != 1) {
    //     //         $user->email_verified_at = date('Y-m-d H:m:s');
    //     //     }
    //     // }

    //     // if ($user->email_verified_at == null) {
    //     //     if ($request->register_by == 'email') {
    //     //         try {
    //     //             $user->notify(new AppEmailVerificationNotification());
    //     //         } catch (\Exception $e) {
    //     //         }
    //     //     } else {
    //     //         $otpController = new OTPVerificationController();
    //     //         $otpController->send_code($user);
    //     //     }
    //     // }

    //     $user->save();
    //     //create token
    //     $user->createToken('tokens')->plainTextToken;

    //     return $this->loginSuccess($user);
    // }

    // public function resendCode()
    // {
    //     $user = auth()->user();
    //     $user->verification_code = rand(100000, 999999);

    //     $user->save();

    //     return response()->json([
    //         'result' => true,
    //         'message' => 'Verification code is sent again',
    //     ], 200);
    // }

    // public function confirmCode(Request $request)
    // {
    //     $user = auth()->user();

    //     if ($user->verification_code == $request->verification_code) {
    //         $user->email_verified_at = date('Y-m-d H:i:s');
    //         $user->verification_code = null;
    //         $user->save();
    //         return response()->json([
    //             'result' => true,
    //             'message' => 'Your account is now verified',
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             'result' => false,
    //             'message' => 'Code does not match, you can request for resending the code',
    //         ], 200);
    //     }
    // }


    // public function socialLogin(Request $request)
    // {
    //     if (!$request->provider) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => 'User not found',
    //             'user' => null
    //         ]);
    //     }

    //     switch ($request->social_provider) {
    //         case 'facebook':
    //             $social_user = Socialite::driver('facebook')->fields([
    //                 'name',
    //                 'first_name',
    //                 'last_name',
    //                 'email'
    //             ]);
    //             break;
    //         case 'google':
    //             $social_user = Socialite::driver('google')
    //                 ->scopes(['profile', 'email']);
    //             break;
    //         case 'twitter':
    //             $social_user = Socialite::driver('twitter');
    //             break;
    //         case 'apple':
    //             $social_user = Socialite::driver('sign-in-with-apple')
    //                 ->scopes(['name', 'email']);
    //             break;
    //         default:
    //             $social_user = null;
    //     }
    //     if ($social_user == null) {
    //         return response()->json(['result' => false, 'message' => 'No social provider matches', 'user' => null]);
    //     }

    //     if ($request->social_provider == 'twitter') {
    //         $social_user_details = $social_user->userFromTokenAndSecret($request->access_token, $request->secret_token);
    //     } else {
    //         $social_user_details = $social_user->userFromToken($request->access_token);
    //     }

    //     if ($social_user_details == null) {
    //         return response()->json(['result' => false, 'message' => 'No social account matches', 'user' => null]);
    //     }

    //     $existingUserByProviderId = User::where('provider_id', $request->provider)->first();

    //     if ($existingUserByProviderId) {
    //         $existingUserByProviderId->access_token = $social_user_details->token;
    //         if ($request->social_provider == 'apple') {
    //             $existingUserByProviderId->refresh_token = $social_user_details->refreshToken;
    //             if (!isset($social_user->user['is_private_email'])) {
    //                 $existingUserByProviderId->email = $social_user_details->email;
    //             }
    //         }
    //         $existingUserByProviderId->save();
    //         return $this->loginSuccess($existingUserByProviderId);
    //     } else {
    //         $existing_or_new_user = User::firstOrNew(
    //             [['email', '!=', null], 'email' => $social_user_details->email]
    //         );

    //         $existing_or_new_user->user_type = 'customer';
    //         $existing_or_new_user->provider_id = $social_user_details->id;

    //         if (!$existing_or_new_user->exists) {
    //             if ($request->social_provider == 'apple') {
    //                 if ($request->name) {
    //                     $existing_or_new_user->name = $request->name;
    //                 } else {
    //                     $existing_or_new_user->name = 'Apple User';
    //                 }
    //             } else {
    //                 $existing_or_new_user->name = $social_user_details->name;
    //             }
    //             $existing_or_new_user->email = $social_user_details->email;
    //             $existing_or_new_user->email_verified_at = date('Y-m-d H:m:s');
    //         }

    //         $existing_or_new_user->save();

    //         return $this->loginSuccess($existing_or_new_user);
    //     }
    // }


    // public function account_deletion()
    // {
    //     if (auth()->user()) {
    //         Cart::where('user_id', auth()->user()->id)->delete();
    //     }

    //     // if (auth()->user()->provider && auth()->user()->provider != 'apple') {
    //     //     $social_revoke =  new SocialRevoke;
    //     //     $revoke_output = $social_revoke->apply(auth()->user()->provider);

    //     //     if ($revoke_output) {
    //     //     }
    //     // }

    //     $auth_user = auth()->user();
    //     $auth_user->tokens()->where('id', $auth_user->currentAccessToken()->id)->delete();
    //     $auth_user->customer_products()->delete();

    //     User::destroy(auth()->user()->id);

    //     return response()->json([
    //         "result" => true,
    //         "message" => 'Your account deletion successfully done'
    //     ]);
    // }

    // public function getUserInfoByAccessToken(Request $request)
    // {
    //     $token = PersonalAccessToken::findToken($request->access_token);
    //     if (!$token) {
    //         return $this->loginFailed();
    //     }
    //     $user = $token->tokenable;

    //     if ($user == null) {
    //         return $this->loginFailed();
    //     }

    //     return $this->loginSuccess($user, $request->access_token);
    // }
    // public function forgotPasswordApi(Request $request)
    // {
    //     try {
    //     $data = $request ->all();
    //     // dd($data);
    //     $rule = [
    //         'email' => 'required|exists:users,email',
    //     ];
    //     $message = [
    //         'email.required' => 'vui lòng nhập email hợp lệ',
    //         'email.exists' => 'email này không tồn tại',
    //     ];
    //     $validator = Validator::make($data, $rule, $message);
    //     if($validator->fails()) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => 'Mail không hợp lệ',
    //         ],400);
    //     }
    //     $token = strtoupper(Str::random(10));
    //     $user = User::where('email',$data['email'])->first();
    //     $key  = rand(100000,999999);
    //     $user->OTP = $key;
    //     $user->device_token = $token;
    //     $user->save();
    //     // dd(env('MAIL_FROM_ADDRESS'));
    //     Mail::send('email.check_email_forget', compact('user','key'), function ($message) use ($user,$key) {
    //         $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    //         $message->to($user->email, $user->name)->subject('Lấy lại mật khẩu');
    //     });
    //     return response()->json(
    //         [
    //             'result' => true,
    //             'message' => "Vui lòng kiểm tra email"
    //         ]
    //     );
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => "Gửi email thất bại",
    //             'error' => $e->getMessage()
    //         ], 400);
    //     }
    // }

    // public function resendMail(Request $request)
    // {
    //     try {
    //     $data = $request ->all();
    //     $token = strtoupper(Str::random(10));
    //     $user = User::where('email',$data['email'])->first();
    //     $key  = rand(100000,999999);
    //     $user->OTP = $key;
    //     $user->device_token = $token;
    //     $user->save();
    //     // dd(env('MAIL_FROM_ADDRESS'));
    //     Mail::send('email.check_email_forget', compact('user','key'), function ($message) use ($user,$key) {
    //         $message->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    //         $message->to($user->email, $user->name)->subject('Lấy lại mật khẩu');
    //     });
    //     return response()->json(
    //         [
    //             'result' => true,
    //             'message' => "Vui lòng kiểm tra email"
    //         ]
    //     );
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => "Gửi email thất bại",
    //             'error' => $e->getMessage()
    //         ], 400);
    //     }
    // }

    // public function appConfirmCode(Request $request){
    //     $data = $request->all();
    //     $rule = [
    //         'code' => 'required',
    //     ];
    //     $message = [
    //         'code.required' => 'vui lòng nhập mã xác nhận',
    //     ];
    //     $validator = Validator::make($data, $rule, $message);
    //     if($validator->fails()) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => 'Mã xác nhận không hợp lệ',
    //         ],400);
    //     }
    //     $user = User::where('email',$data['email'])->first();
    //     if($user->OTP == $data['code']) {
    //         // Random lại mã OTP
    //         $key  = rand(100000,999999);
    //         $user->OTP = $key;
    //         $user->save();
    //         return response()->json([
    //             'result' => true,
    //             'message' => 'Xác nhận thành công',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'result' => false,
    //             'message' => 'Mã xác nhận không chính xác',
    //         ]);
    //     }
    // }

    // public function resetPassword(Request $request)
    // {
    //     $data = $request->all();
    //     $rule = [
    //         'password' => 'required|min:6',
    //         'confirm_password' => 'required|same:password',
    //     ];
    //     $message = [
    //         'password.required' => 'vui lòng nhập mật khẩu',
    //         'password.min' => 'mật khẩu phải có ít nhất 6 ký tự',
    //         'confirm_password.required' => 'vui lòng nhập lại mật khẩu',
    //         'confirm_password.same' => 'mật khẩu không trùng khớp',
    //     ];
    //     $validator = Validator::make($data, $rule, $message);
    //     if($validator->fails()) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => 'Nhập liệu không hợp lệ',
    //         ],400);
    //     }
    //     $user = User::where('email',$data['email'])->first();
    //     $user->password = Hash::make($data['password']);
    //     $user->save();
    //     return response()->json([
    //         'result' => true,
    //         'message' => 'Đổi mật khẩu thành công',
    //     ]);
    // }

    // public function changePassword(Request $request)
    // {
    //     $data = $request->all();
    //     $rule = [
    //         'old_password' => 'required',
    //         'password' => 'required',
    //         'confirm_password' => 'required|same:password',
    //     ];
    //     $message = [
    //         'old_password.required' => 'vui lòng nhập mật khẩu cũ',
    //         'password.required' => 'vui lòng nhập mật khẩu mới',
    //         'password.min' => 'mật khẩu phải có ít nhất 6 ký tự',
    //         'confirm_password.required' => 'vui lòng nhập lại mật khẩu',
    //         'confirm_password.same' => 'mật khẩu không trùng khớp',
    //     ];
    //     $validator = Validator::make($data, $rule, $message);
    //     if($validator->fails()) {
    //         return response()->json([
    //             'result' => false,
    //             'message' => 'Nhập liệu không hợp lệ',
    //         ],400);
    //     }
    //     $user = Auth::user();
    //     if(Hash::check($data['old_password'], $user->password)) {
    //         $user->password = Hash::make($data['password']);
    //         $user->save();
    //         return response()->json([
    //             'result' => true,
    //             'message' => 'Đổi mật khẩu thành công',
    //         ]);
    //     } else {
    //         return response()->json([
    //             'result' => false,
    //             'message' => 'Mật khẩu cũ không chính xác',
    //         ]);
    //     }
    // }
}
