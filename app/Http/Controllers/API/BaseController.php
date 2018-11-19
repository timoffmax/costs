<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Create a new controller instance.
     * Require auth.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');
    }
}
