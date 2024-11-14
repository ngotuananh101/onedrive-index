<?php

namespace App\Http\Controllers;

use Artisan;
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

                // Retrieve user info from OneDrive
                $this->me();

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
        // Clear the cache
        Artisan::call('responsecache:clear');
        // Display the third step of the authentication process
        return view('auth.step3');
    }

    /**
     * Refresh the OneDrive access token
     *
     * This function requests a new access token from OneDrive using the refresh token stored in the cache.
     * It is used to maintain the validity of the access token to ensure continuous access to OneDrive APIs.
     *
     * @return bool Returns true upon successful acquisition of the access token, otherwise returns false.
     */
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

    /**
     * Retrieves user information from OneDrive and caches it
     *
     * This method first attempts to retrieve the user's access token from the cache.
     * If successful, it uses the access token to make a GET request to OneDrive's user information API.
     * If the request is successful (HTTP status code 200), it processes the returned user information,
     * attempts to retrieve the user's photo from OneDrive, and if unsuccessful, generates a default avatar URL.
     * Finally, it adds the user information and photo to the cache.
     */
    public function me()
    {
        // Retrieve the access token from the cache
        $accessToken = cache('one_drive_access_token');
        // Get user info from OneDrive
        $res = Http::withToken($accessToken)->get(config('onedrive.user_api_url'));

        // If the request is successful, process the returned user information
        if ($res->status() === 200) {
            $user = $res->json();
            // Get photo from OneDrive
            $photo = $this->getPhoto($accessToken);
            // If the photo retrieval fails, generate a default avatar URL
            if (!$photo) {
                $photo = 'https://ui-avatars.com/api/?background=random&name=' . $user['displayName'];
            }
            // Add the photo to the user information
            $user['photo'] = $photo;
            // Cache the user information
            cache(['one_drive_user' => $user]);
        }
    }

    /**
     * Retrieves the user's photo from OneDrive and saves it locally.
     *
     * This function uses the provided accessToken to authenticate with OneDrive and attempts to download the user's photo.
     * If successful, it saves the photo locally and returns the URL of the saved photo. If the request fails, it outputs
     * the error information and returns null.
     *
     * @param string $accessToken The user's access token for accessing OneDrive.
     * @return string|null The URL of the saved photo, or null if the operation failed.
     */
    public function getPhoto($accessToken)
    {
        // Get photo from OneDrive
        $res = Http::withToken($accessToken)->get(config('onedrive.user_api_url') . '/photo/$value');
        if ($res->status() === 200) {
            // Save the obtained photo locally
            file_put_contents(public_path('assets/media/avatar/photo.jpg'), $res->body());
            // Return the URL of the saved photo
            return asset('assets/media/avatar/photo.jpg');
        } else {
            // Log error information upon failure to obtain the photo
            Log::error($res->body());
            return null;
        }
    }
}
