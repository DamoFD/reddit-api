<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Types\AccessToken;

class RedditService
{
    /**
     * The Reddit OAuth2 token URL.
     */
    protected string $tokenURL = "https://reddit.com/api/v1/access_token";

    /**
     * The required data for the token request.
     */
    protected array $tokenData = [
        "client_id" => NULL,
        "client_secret" => NULL,
        "grant_type" => "authorization_code",
        "code" => NULL,
        "redirect_uri" => NULL,
        "user_agent" => NULL
    ];

    /**
     * UserService constructor.
     */
    public function __construct()
    {
        $this->tokenData['client_id'] = config('larascord.client_id');
        $this->tokenData['client_secret'] = config('larascord.client_secret');
        $this->tokenData['grant_type'] = config('larascord.grant_type');
        $this->tokenData['redirect_uri'] = config('larascord.redirect_uri');
        $this->tokenData['user_agent'] = config('larascord.user_agent');
    }

    /**
     * Handles the Reddit OAuth2 callback and returns the access token.
     *
     * @throws RequestException
     */
    public function getAccessTokenFromCode(string $code): AccessToken
    {
        $this->tokenData['code'] = $code;

        $response = Http::withHeaders([
            'User-Agent' => $this->tokenData['user_agent'],
            'Authorization' => 'Basic ' . base64_encode($this->tokenData['client_id'] . ':' . $this->tokenData['client_secret']),
        ])->asForm()->post($this->tokenURL, $this->tokenData);

        dd($response->throw());

        return new AccessToken(json_decode($response->body()));
    }
}
