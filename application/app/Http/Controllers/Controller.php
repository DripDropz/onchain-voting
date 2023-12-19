<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function unauthenticated($request): void
    {
        abort(response()->json(
            [
                'api_status' => '401',
                'message' => 'UnAuthenticated',
            ], 401));
    }
}
