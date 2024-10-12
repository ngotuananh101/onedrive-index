<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OauthController extends Controller
{
    public mixed $one_drive_client_id;
    public mixed $one_drive_client_secret;
    public mixed $one_drive_redirect_uri;
    public mixed $one_drive_scope;
    public mixed $one_drive_access_token;
    public mixed $one_drive_refresh_token;

    // Init Oath
    public function __construct()
    {
        $this->one_drive_client_id = env('VITE_ONE_DRIVE_CLIENT_ID', '');
        $this->one_drive_client_secret = env('VITE_ONE_DRIVE_CLIENT_SECRET', '');
        $this->one_drive_redirect_uri = env('VITE_ONE_DRIVE_REDIRECT_URI', '');
        $this->one_drive_scope = explode(" ", env('VITE_ONE_DRIVE_SCOPE', ''));
        $this->one_drive_access_token = cache('one_drive_access_token');
        $this->one_drive_refresh_token = cache('one_drive_refresh_token');
    }

    /**
     * Kiểm tra trạng thái đăng nhập
     *
     * @return \Illuminate\Http\JsonResponse Trả về kết quả dưới dạng JSON
     */
    public function checkLogin()
    {
        // Kiểm tra xem refresh token đã được lưu trữ hay chưa
        if ($this->one_drive_refresh_token === null) {
            return response()->json([
                'status' => '401',
                'message' => 'Chưa đăng nhập',
            ]);
        }

        // Khai báo xem có cần làm mới token hay không
        $need_refresh = !$this->one_drive_access_token;

        // Lấy access token mới nếu cần
        if ($need_refresh) {
            try {
                $refresh_success = $this->refreshToken();
            } catch (\Exception $e) {
                return response()->json([
                    'status' => '500',
                    'message' => 'Lỗi khi làm mới token',
                ]);
            }
        } else {
            $refresh_success = true;
        }

        // Trả về kết quả
        return response()->json([
            'status' => '200',
            'message' => 'Đã đăng nhập',
            'need_refresh' => $need_refresh,
            'refresh_success' => $refresh_success,
        ]);
    }

    /**
     * Lấy access token và refresh token từ code nhận được
     *
     * @param Request $request Đối tượng yêu cầu để lấy thông tin code
     * @return \Illuminate\Http\JsonResponse Trả về kết quả dưới dạng JSON
     */
    public function getToken(Request $request)
    {
        // Xác thực rằng tham số 'code' là bắt buộc và phải là chuỗi
        $request->validate($request, ['code' => 'required|string']);

        try {
            // Lấy giá trị của code từ yêu cầu
            $code = $request->input('code');
            // URL để yêu cầu access token
            $url = env('VITE_ONE_DRIVE_TOKEN_API_URL');

            // Dữ liệu cần gửi để lấy token
            $data = [
                'client_id' => $this->one_drive_client_id,
                'client_secret' => $this->one_drive_client_secret,
                'code' => $code,
                'redirect_uri' => $this->one_drive_redirect_uri,
                'grant_type' => 'authorization_code'
            ];

            // Gửi yêu cầu POST để lấy token
            $res = Http::asForm()->post($url, $data);

            // Nếu yêu cầu thành công (trạng thái 200)
            if ($res->status() === 200) {
                // Phân tích kết quả trả về
                $body = $res->json();
                // Lấy thời gian hết hạn của token
                $expires_in = $body['expires_in'];
                // Lưu access token vào thuộc tính lớp
                $this->one_drive_access_token = $body['access_token'];
                // Lưu access token vào cache với thời gian hết hạn
                cache(['one_drive_access_token' => $this->one_drive_access_token], $expires_in);
                // Lưu refresh token vào thuộc tính lớp
                $this->one_drive_refresh_token = $body['refresh_token'];
                // Lưu refresh token vào cache
                cache(['one_drive_refresh_token' => $this->one_drive_refresh_token]);
                // Thêm trường trạng thái vào kết quả và trả về
                $body['status'] = '200';
                return response()->json($body);
            } else {
                // Nếu yêu cầu không thành công, trả lại kết quả gốc
                return response()->json($res->json(), $res->status());
            }
        } catch (\Exception $e) {
            // Ghi nhật ký lỗi
            Log::error('Error getting token: ' . $e->getMessage());
            // Trả về thông báo lỗi với mã trạng thái 500
            return response()->json([
                'error_description' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Làm mới access token
     *
     * @return bool Trả về true nếu làm mới thành công, false nếu thất bại
     */
    public function refreshToken()
    {
        try {
            // URL để gọi API làm mới token
            $url = env('VITE_ONE_DRIVE_TOKEN_API_URL');

            // Dữ liệu cần提交 để làm mới token
            $data = [
                'client_id' => $this->one_drive_client_id,
                'client_secret' => $this->one_drive_client_secret,
                'refresh_token' => $this->one_drive_refresh_token,
                'redirect_uri' => $this->one_drive_redirect_uri,
                'grant_type' => 'refresh_token'
            ];

            // Gọi API để làm mới token
            $res = Http::asForm()->post($url, $data);

            // Kiểm tra nếu trả về status 200则 cho rằng làm mới token thành công
            if ($res->status() === 200) {
                // Lấy kết quả trả về ở định dạng json
                $body = $res->json();

                // Lấy thời gian hết hạn của access token
                $expires_in = $body['expires_in'];

                // Cập nhật access token
                $this->one_drive_access_token = $body['access_token'];

                // Lưu access token vào cache với thời gian hết hạn tương ứng
                cache(['one_drive_access_token' => $this->one_drive_access_token], $expires_in);

                // Trả về true chỉ ra rằng làm mới token thành công
                return true;
            } else {
                // Trả về false chỉ ra rằng làm mới token thất bại
                return false;
            }
        } catch (\Exception $e) {
            // Ghi nhật ký lỗi khi xảy ra ngoại lệ
            Log::error('Error refreshing token: ' . $e->getMessage());

            // Trả về false chỉ ra rằng làm mới token thất bại
            return false;
        }
    }
}
