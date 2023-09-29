<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RedditService;

class UserController extends Controller
{
    public function redirectToReddit()
    {
        $state = bin2hex(random_bytes(16));

        session(['state' => $state]);

        $queryParams = http_build_query([
            'client_id' => config('larascord.client_id'),
            'redirect_uri' => config('larascord.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'identity',
            'state' => $state
        ]);

        $url = 'https://www.reddit.com/api/v1/authorize?' . $queryParams;

        return redirect($url);
    }

    public function get(Request $request)
    {
        if ($request->get('state') !== session('state')) {
            return response('Invalid state parameter', 422);
        }
        try {
            $accessToken = (new RedditService())->getAccessTokenFromCode($request->get('code'));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid code', 'message' => $e->getMessage()], 400);
        }
    }
}
