<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RedditService;

class UserController extends Controller
{
    public function redirectToReddit()
    {
        $queryParams = http_build_query([
            'client_id' => config('larascord.client_id'),
            'redirect_uri' => config('larascord.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'identity',
            'state' => bin2hex(random_bytes(16)),
        ]);

        $url = 'https://www.reddit.com/api/v1/authorize?' . $queryParams;

        return redirect($url);
    }

    public function get(Request $request)
    {
        try {
            $accessToken = (new RedditService())->getAccessTokenFromCode($request->get('code'));
        } catch (\Exception $e) {
            return $this->throwError('invalid_code', $e);
        }
    }
}
