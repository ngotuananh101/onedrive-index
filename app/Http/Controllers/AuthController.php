<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Log;

class AuthController extends Controller
{
    public function step1()
    {
        return view('auth.step1');
    }

    public function step2()
    {
        $authUrl = config('onedrive.auth_api_url')
            . '?client_id='
            . config('onedrive.client_id')
            . '&response_type=code&redirect_uri='
            . config('onedrive.redirect_uri')
            . '&response_mode=query&scope='
            . str_replace(' ', '+', config('onedrive.scope'));
        return view('auth.step2', [
            'authUrl' => $authUrl,
        ]);
    }

    public function callback(Request $request)
    {
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
            $res = Http::asForm()->post(config('onedrive.token_api_url'), $data);
            if ($res->status() === 200) {
                $body = $res->json();
                $expires_in = $body['expires_in'];
                cache(['one_drive_access_token' => $body['access_token']], $expires_in);
                cache(['one_drive_refresh_token' => $body['refresh_token']]);
                return redirect()->route('auth.step3');
            } else {
                Log::error($res->body());
                return redirect()->route('auth.step2')->with('error', __('Can not get access token. For more information please check application log.'));
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('auth.step2')->with('error', __('Can not get access token. For more information please check application log.'));
        }
    }
}
