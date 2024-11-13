<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class AuthController extends Controller
{
    /**
     * Displays the first step of the authentication process
     *
     * This function returns the view for the first step of the authentication process,
     * guiding the user through the authentication process step by step.
     *
     * @return \Illuminate\View\View
     */
    public function step1()
    {
        // Display the first step of the authentication process
        return view('auth.step1');
    }

    /**
     * Displays the page for the second step of authentication.
     *
     * This method constructs the OneDrive authorization URL and passes it to the front end for user redirection.
     * The user will need to log in and authorize the application through this URL to obtain an authorization code.
     * This code will then be used to request an access token.
     *
     * @return \Illuminate\View\View Returns the view for the second step of authentication, passing in the authorization URL.
     */
    public function step2()
    {
        // Construct the authorization URL
        $authUrl = config('onedrive.auth_api_url')
            . '?client_id='
            . config('onedrive.client_id')
            . '&response_type=code&redirect_uri='
            . config('onedrive.redirect_uri')
            . '&response_mode=query&scope='
            . str_replace(' ', '+', config('onedrive.scope'));
        // Display the second step of the authentication process
        return view('auth.step2', [
            'authUrl' => $authUrl,
        ]);
    }

    /**
     * Handles the callback request from OneDrive authorization server.
     *
     * This function is responsible for exchanging the authorization code for an access token,
     * and then caching the access token and refresh token for subsequent API calls.
     *
     * @param Request $request The incoming request object, expected to contain the authorization code.
     * @return \Illuminate\Http\RedirectResponse Redirects to the appropriate step based on the outcome of the token acquisition process.
     */
    public function callback(Request $request)
    {
        // Retrieve the authorization code from the request
        $code = $request->input('code');

        // Data required to request the token
        $data = [
            'client_id' => config('onedrive.client_id'),
            'client_secret' => config('onedrive.client_secret'),
            'code' => $code,
            'redirect_uri' => config('onedrive.redirect_uri'),
            'grant_type' => 'authorization_code'
        ];

        try {
            // Post request to OneDrive to obtain the access token
            $res = Http::asForm()->post(config('onedrive.token_api_url'), $data);

            if ($res->status() === 200) {
                // Parse the response body
                $body = $res->json();

                // Cache the access token and refresh token
                $expires_in = $body['expires_in'];
                cache(['one_drive_access_token' => $body['access_token']], $expires_in);
                cache(['one_drive_refresh_token' => $body['refresh_token']]);

                // Redirect to the third step of authentication upon successful acquisition of the token
                return redirect()->route('auth.step3');
            } else {
                // Log error information upon failure to obtain the token
                Log::error($res->body());

                // Redirect back to the second step of authentication with an error message upon failure
                return redirect()->route('auth.step2')->with('error', __('Can not get access token. For more information please check application log.'));
            }
        } catch (\Exception $e) {
            // Log exception information
            Log::error($e->getMessage());

            // Redirect back to the second step of authentication with an error message upon encountering an exception
            return redirect()->route('auth.step2')->with('error', __('Can not get access token. For more information please check application log.'));
        }
    }

    /**
     * Displays the third step of the authentication process.
     *
     * This function displays the third step of the authentication process, indicating that the user has successfully authenticated.
     *
     * @return \Illuminate\View\View Returns the view for the third step of authentication.
     */
    public function step3()
    {
        // Display the third step of the authentication process
        return view('auth.step3');
    }

    public function refreshToken()
    {
        // Data required to request the token
        $data = [
            'client_id' => config('onedrive.client_id'),
            'client_secret' => config('onedrive.client_secret'),
            'refresh_token' => cache('one_drive_refresh_token'),
            'redirect_uri' => config('onedrive.redirect_uri'),
            'grant_type' => 'refresh_token'
        ];

        try {
            // Post request to OneDrive to obtain the access token
            $res = Http::asForm()->post(config('onedrive.token_api_url'), $data);

            if ($res->status() === 200) {
                // Parse the response body
                $body = $res->json();

                // Cache the access token and refresh token
                $expires_in = $body['expires_in'];
                cache(['one_drive_access_token' => $body['access_token']], $expires_in);

                // Return true upon successful acquisition of the token
                return true;
            } else {
                // Log error information upon failure to obtain the token
                Log::error($res->body());

                // Return false upon failure to obtain the token
                return false;
            }
        } catch (\Exception $e) {
            // Log exception information
            Log::error($e->getMessage());

            // Return false upon encountering an exception
            return false;
        }
    }
}
