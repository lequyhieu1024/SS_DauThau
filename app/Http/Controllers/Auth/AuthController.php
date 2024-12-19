<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\ChangePasswordRequest;
use App\Jobs\SendForgotPasswordJob;
use App\Models\User;
use App\Repositories\EnterpriseRepository;
use App\Repositories\StaffRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    protected $userRepository;
    protected $enterpriseRepository;
    protected $staffRepository;
    public function __construct(UserRepository $userRepository, EnterpriseRepository $enterpriseRepository, StaffRepository $staffRepository)
    {
        $this->userRepository = $userRepository;
        $this->enterpriseRepository = $enterpriseRepository;
        $this->staffRepository = $staffRepository;
    }

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

        $validator = Validator::make($request->all(), [
            'taxcode' => [
                'required_without:email',
                function ($attribute, $value, $fail) {
                    $taxcodePattern = '/^[0-9]{10,14}$/';
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

        $credentials = $request->only('taxcode', 'email', 'password');
        $credentials = array_filter($credentials); // Xóa các trường rỗng

        try {
            $user = User::where('taxcode', $request->taxcode)
                ->orWhere('email', $request->email)
                ->first();

            if ($user->account_ban_at != null) {
                return response()->json(
                    ['result' => false, 'message' => 'Tài khoản đã bị cấm'],
                    400
                );
            }

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(
                    ['result' => false, 'message' => 'Tài khoản hoặc mật khẩu không chính xác'],
                    401
                );
            }

            return $this->respondWithToken($token, $user);
        } catch (JWTException $e) {
            return response()->json(['result' => false, 'message' => 'Không thể tạo token'], 500);
        }
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'refresh_token' => JWTAuth::fromUser($user, ['refresh' => true]),
            ]
        ]);
    }

    public function refreshToken(Request $request)
    {
        try {
            $token = $request->header('Authorization');
            $token = str_replace('Bearer ', '', $token);

            $payload = JWTAuth::setToken($token)->getPayload();

            $user = JWTAuth::setToken($token)->authenticate();

            if (!$user) {
                return response()->json(['result' => false, 'message' => 'Token không hợp lệ'], 401);
            }

            $newToken = JWTAuth::fromUser($user);

            return response()->json([
                'result' => true,
                'access_token' => $newToken,
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json(['result' => false, 'message' => 'Token đã hết hạn'], 401);
        } catch (JWTException $e) {
            return response()->json(['result' => false, 'message' => 'Không thể làm mới token'], 500);
        }
    }



    public function profile()
    {
        $user = JWTAuth::user();
        if ($user->staff && $user->staff->avatar) {
            $user->staff->avatar = env('APP_URL') .'/'. $user->staff->avatar;
        }
        if ($user->enterprise && $user->enterprise->avatar) {
            $user->enterprise->avatar = env('APP_URL') .'/'. $user->enterprise->avatar;
        }
        return response()->json([
            'result' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'taxcode' => $user->taxcode,
                'email' => $user->email,
                'avatar' => $user->staff && !empty($user->staff) ? $user->staff->avatar : $user->enterprise->avatar,
                'email_verified' => $user->email_verified_at != null,
                'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
            ]
        ]);
    }

    public function editProfile()
    {
        $user = JWTAuth::user();
        if ($user->staff && $user->staff->avatar) {
            $user->staff->avatar = env('APP_URL') .'/'. $user->staff->avatar;
        }
        if ($user->enterprise && $user->enterprise->avatar) {
            $user->enterprise->avatar = env('APP_URL') .'/'. $user->enterprise->avatar;
        }
        return response()->json([
            'result' => true,
            'message' => "Lấy thông tin cá nhân thành công",
            'data' => [
                'id' => $user->id,
                'account_type' => isset($user->staff) ? 'staff' : 'enterprise',
                'name' => $user->name,
                'taxcode' => $user->taxcode,
                'email' => $user->email,
                'profile' => $user->staff ?? $user->enterprise
            ]
        ]);
    }

    public function updateProfile(Request $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->all();
            $user = $this->userRepository->findOrFail(Auth::user()->id);
            if ($data['account_type'] == 'enterprise') {
                $rules = [
                    'name' => 'required|max:50',
                    'taxcode' => 'required',
                    'email' => 'email',
                    'representative' => 'required|max:191',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'phone' =>  [
                        'required',
                        'regex:/^(\(\+84\s?\d{1,2}\)|\+84|\(0\d{1,2}\)|0\d{1,2})(\s?\d{3,4})(\s?\d{3,4})$/'
                    ],
                    'address' => 'required|max:191',
                    'website' => 'required|max:191',
                    'establish_date' => 'required|date|before:today|after_or_equal:1900-01-01',
                    'registration_date' => 'required|date|before:today|after_or_equal:1900-01-01',
                    'registration_number' => 'required|max:50',
                    'organization_type' => 'required|in:1,2',
                    'industry_id' => 'required|array|exists:industries,id',
                ];
                $validator = Validator::make($data, $rules);
                if ($request->hasFile('avatar')) {
                    if ($user->enterprise && $user->enterprise->avatar && file_exists($user->enterprise->avatar)) {
                        unlink($user->enterprise->avatar);
                    }
                    if ($user->staff && $user->staff->avatar && file_exists($user->staff->avatar)) {
                        unlink($user->staff->avatar);
                    }
                    $data['avatar'] = upload_image($request->file('avatar'));
                }
                if ($validator->fails()) {
                    return response()->json(['result' => false, 'message' => $validator->errors()], 422);
                }
                $this->enterpriseRepository->update($data, $user->enterprise->id);
            }
            if ($data['account_type'] == 'staff') {
                $rules = [
                    'name' => 'required',
                    'birthday' => 'required',
                    'gender' => 'required',
                    'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                    'taxcode' => 'required',
                    'email' => 'required',
                    'phone' => [
                        'required',
                        'regex:/^(\(\+84\s?\d{1,2}\)|\+84|\(0\d{1,2}\)|0\d{1,2})(\s?\d{3,4})(\s?\d{3,4})$/',
                    ],
                ];
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    return response()->json(['result' => false, 'message' => $validator->errors()], 422);
                }
                if ($request->hasFile('avatar')) {
                    if ($user->staff && $user->staff->avatar && file_exists($user->staff->avatar)) {
                        unlink($user->staff->avatar);
                    }
                    $data['avatar'] = upload_image($request->file('avatar'));
                }
                $this->staffRepository->update($data, $user->staff->id);
            }
            $this->userRepository->update($data, Auth::user()->id);
            DB::commit();
            return response([
                'result' => true,
                'message' => "Cập nhật thông tin thành công"
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['result' => false, 'message' => 'Có lỗi sảy ra, ' . $e], 500);
        }
    }

    public function sendMailForPasswordReset(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = $this->userRepository->firstWhere('email', $request->input('email'));
            if (!$user) {
                return response([
                    'result' => false,
                    'message' => "Người dùng không tồn tại",
                ], 200);
            }
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->input('email')],
                [
                    'token' => $token,
                    'created_at' => now(),
                ]
            );
            $userData = $user->toArray();
            $userData['token'] = $token;
            SendForgotPasswordJob::dispatch($userData);
            DB::commit();
            return response([
                'result' => true,
                'message' => 'Gửi email cập nhật mật khẩu mới thành công',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['result' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request) {
        $data = $request->all();
        $resetToken = DB::table('password_reset_tokens')->where('email', $data['email'])->first();
        if (!$resetToken || $resetToken->token !== $data['token']) {
            return response(['result' => false, 'message' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.'], 400);
        }
        $user = $this->userRepository->firstWhere('email', $data['email']);
        $data = array_intersect_key($data, ['password' => '']);
        $this->userRepository->update($data, $user->id);
        return response([
            'result' => true,
            'message' => 'Cập nhật mật khẩu mới thành công',
        ], 200);
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

}
