<?php
namespace App\Services;

use GuzzleHttp\Client;
use App\Models\GoogleToken;
use Google_Service_Calendar;
use Google_Client;

class GoogleService
{
    public function client($userId = null)
    {
        $client = new Google_Client(); 
        $client->setAuthConfig(storage_path('app/public/google/client_secret.json'));
        $client->addScope(Google_Service_Calendar::CALENDAR);
        $client->setRedirectUri(url('/oauth/google/callback'));
        $client->setAccessType('offline'); // IMPORTANT to get refresh_token
        $client->setPrompt('consent');

        if ($userId) {
            $token = GoogleToken::where('user_id', $userId)->first();
            if ($token) {
                $client->setAccessToken($token->access_token);

                if ($client->isAccessTokenExpired()) {
                    $refreshToken = $token->refresh_token;
                    if ($refreshToken) {
                        $new = $client->fetchAccessTokenWithRefreshToken($refreshToken);
                        // merge tokens and save
                        $access = $client->getAccessToken();
                        $token->access_token = $access;
                        // refresh_token is only returned on first consent; keep existing if null
                        if (isset($new['refresh_token'])) {
                            $token->refresh_token = $new['refresh_token'];
                        }
                        $token->expires_at = now()->addSeconds($access['expires_in'] ?? 3600);
                        $token->save();
                        $client->setAccessToken($access);
                    }
                }
            }
        }

        return $client;
    }
}
