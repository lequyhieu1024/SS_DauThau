<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use OpenApi\Annotations as OA;

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

        // Xác thực yêu cầu
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

        // Lấy thông tin xác thực
        $credentials = $request->only('taxcode', 'email', 'password', 'account_ban_at');
        $credentials = array_filter($credentials); // Xóa các trường rỗng

        try {
            $user = User::where('taxcode', $request->taxcode)
                ->orWhere('email', $request->email)
                ->first();
            if ($user->account_ban_at != NULL) {
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
        } catch (JWTException $e) {
            return response()->json(['result' => false, 'message' => 'Không thể tạo token'], 500);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
                'refresh_token' => JWTAuth::fromUser(auth()->user(), ['refresh' => true])
            ]
        ]);
    }

    public function refreshToken(Request $request)
    {
        try {
            $token = $request->header('Authorization');

            if (!$user = JWTAuth::setToken($token)->authenticate()) {
                return response()->json(['result' => false, 'message' => 'Token không hợp lệ'], 401);
            }

            // Tạo token mới
            $newToken = JWTAuth::fromUser($user);

            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            return response()->json(['result' => false, 'message' => 'Không thể làm mới token'], 500);
        }
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

    public function editProfile()
    {
        $user = JWTAuth::user();
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
            $this->userRepository->update($data, Auth::user()->id);
            if ($data['account_type'] == 'enterprise') {
                $this->enterpriseRepository->update($data, $user->enterprise->id);
            }
            if ($data['account_type'] == 'staff') {
                $this->staffRepository->update($data, $user->staff->id);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['result' => false, 'message' => 'Có lỗi sảy ra, ' . $e], 500);
        }
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
