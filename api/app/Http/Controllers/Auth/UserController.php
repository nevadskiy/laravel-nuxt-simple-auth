<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
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
