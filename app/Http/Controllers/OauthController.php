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
     * Handle Callback
     *
     * @throws GuzzleException
     */
    public function getToken(Request $request)
    {
        $request->validate($request, ['code' => 'required|string']);
        try {
            $code = $request->input('code');
            $url = env('VITE_ONE_DRIVE_TOKEN_API_URL');
            $data = [
                'client_id' => $this->one_drive_client_id,
                'client_secret' => $this->one_drive_client_secret,
                'code' => $code,
                'redirect_uri' => $this->one_drive_redirect_uri,
                'grant_type' => 'authorization_code'
            ];
            $res = Http::asForm()->post($url, $data);
            if ($res->status() === 200) {
                $body = $res->json();
                $expires_in = $body['expires_in'];
                $this->one_drive_access_token = $body['access_token'];
                cache(['one_drive_access_token' => $this->one_drive_access_token], $expires_in);
                $this->one_drive_refresh_token = $body['refresh_token'];
                cache(['one_drive_refresh_token' => $this->one_drive_refresh_token]);
                $body['status'] = '200';
                return response()->json($body);
            } else {
                return response()->json($res->json(), $res->status());
            }
        } catch (\Exception $e) {
            Log::error('Error getting token: ' . $e->getMessage());
            return response()->json([
                'error_description' => $e->getMessage(),
            ], 500);
        }
    }

    public function refreshToken()
    {
        try {
            $url = env('VITE_ONE_DRIVE_TOKEN_API_URL');
            $data = [
                'client_id' => $this->one_drive_client_id,
                'client_secret' => $this->one_drive_client_secret,
                'refresh_token' => $this->one_drive_refresh_token,
                'redirect_uri' => $this->one_drive_redirect_uri,
                'grant_type' => 'refresh_token'
            ];
            $res = Http::asForm()->post($url, $data);
            if ($res->status() === 200) {
                $body = $res->json();
                $expires_in = $body['expires_in'];
                $this->one_drive_access_token = $body['access_token'];
                cache(['one_drive_access_token' => $this->one_drive_access_token], $expires_in);
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Error refreshing token: ' . $e->getMessage());
            return false;
        }
    }
}
