<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class OauthController extends Controller
{
    // Define class properties for storing OAuth credentials and tokens
    private string $one_drive_client_id;
    private string $one_drive_client_secret;
    private string $one_drive_redirect_uri;
    private ?string $one_drive_access_token;
    private ?string $one_drive_refresh_token;
    private string $token_api_url;

    // Constructor to initialize OAuth credentials from environment variables
    public function __construct()
    {
        $this->one_drive_client_id = env('VITE_ONE_DRIVE_CLIENT_ID', '');
        $this->one_drive_client_secret = env('VITE_ONE_DRIVE_CLIENT_SECRET', '');
        $this->one_drive_redirect_uri = env('VITE_ONE_DRIVE_REDIRECT_URI', '');
        $this->one_drive_access_token = cache('one_drive_access_token');
        $this->one_drive_refresh_token = cache('one_drive_refresh_token');
        $this->token_api_url = env('VITE_ONE_DRIVE_TOKEN_API_URL', '');

        // Throw an exception if the token API URL is not set
        if (empty($this->token_api_url)) {
            throw new \Exception('Token API URL is not set in environment variables.');
        }
    }

    /**
     * Check login status
     *
     * @return JsonResponse JSON response indicating login status
     */
    public function checkLogin(): JsonResponse
    {
        // Check if the refresh token is stored in cache
        if ($this->one_drive_refresh_token === null) {
            return response()->json([
                'status' => '401',
                'message' => 'Not logged in',
            ], 401);
        }

        // Determine if a token refresh is needed
        $need_refresh = !$this->one_drive_access_token;

        try {
            // Refresh the access token if needed
            $refresh_success = $need_refresh ? $this->refreshToken() : true;
        } catch (\Exception $e) {
            // Return an error response if token refresh fails
            return response()->json([
                'status' => '500',
                'message' => 'Error refreshing token: ' . $e->getMessage(),
            ], 500);
        }

        // Return the login status response
        return response()->json([
            'status' => '200',
            'message' => 'Logged in',
            'need_refresh' => $need_refresh,
            'refresh_success' => $refresh_success,
        ]);
    }

    /**
     * Obtain access token and refresh token from the authorization code
     *
     * @param Request $request Request object to obtain the authorization code
     * @return JsonResponse JSON response with token details
     */
    public function getToken(Request $request): JsonResponse
    {
        // Validate the 'code' parameter in the request
        $request->validate(['code' => 'required|string']);
        // Get the authorization code from the request
        $code = $request->input('code');

        // Data required to request the token
        $data = [
            'client_id' => $this->one_drive_client_id,
            'client_secret' => $this->one_drive_client_secret,
            'code' => $code,
            'redirect_uri' => $this->one_drive_redirect_uri,
            'grant_type' => 'authorization_code'
        ];

        try {
            // Send POST request to obtain the tokens
            $res = Http::asForm()->post($this->token_api_url, $data);

            // Check if the request was successful (status 200)
            if ($res->status() === 200) {
                // Parse the response body
                $body = $res->json();
                // Get the token expiration time
                $expires_in = $body['expires_in'];
                // Store the access token in the class property
                $this->one_drive_access_token = $body['access_token'];
                // Cache the access token with its expiration time
                cache(['one_drive_access_token' => $this->one_drive_access_token], $expires_in);
                // Store the refresh token in the class property
                $this->one_drive_refresh_token = $body['refresh_token'];
                // Cache the refresh token
                cache(['one_drive_refresh_token' => $this->one_drive_refresh_token]);
                // Add status field to the response and return it
                $body['status'] = '200';

                return response()->json($body);
            }

            // If the request was not successful, return the original response
            return response()->json($res->json(), $res->status());
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error getting token: ' . $e->getMessage());
            // Return error message with status code 500
            return response()->json([
                'error_description' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refresh the access token
     *
     * @return bool True if the token was refreshed successfully, false otherwise
     */
    public function refreshToken(): bool
    {
        // Data required to refresh the token
        $data = [
            'client_id' => $this->one_drive_client_id,
            'client_secret' => $this->one_drive_client_secret,
            'refresh_token' => $this->one_drive_refresh_token,
            'redirect_uri' => $this->one_drive_redirect_uri,
            'grant_type' => 'refresh_token'
        ];

        try {
            // Send POST request to refresh the token
            $res = Http::asForm()->post($this->token_api_url, $data);

            // Check if the request was successful (status 200)
            if ($res->status() === 200) {
                // Parse the response body
                $body = $res->json();
                // Get the token expiration time
                $expires_in = $body['expires_in'];
                // Update the access token in the class property
                $this->one_drive_access_token = $body['access_token'];
                // Cache the access token with its expiration time
                cache(['one_drive_access_token' => $this->one_drive_access_token], $expires_in);

                // Return true indicating the token was refreshed successfully
                return true;
            }

            // Return false indicating the token refresh failed
            return false;
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error refreshing token: ' . $e->getMessage());

            // Return false indicating the token refresh failed
            return false;
        }
    }
}
