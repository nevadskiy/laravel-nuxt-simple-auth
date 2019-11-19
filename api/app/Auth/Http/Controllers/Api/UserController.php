<?php

namespace App\Auth\Http\Controllers\Api;

use App\Auth\Http\Resources\UserResource;
use App\Core\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Handle a user request.
     *
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        return new UserResource(
            $request->user()
        );
    }
}
