<?php
declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

/**
 * Custom base controller
 */
abstract class BaseController extends Controller
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
