<?php

namespace Module\Auth\Http\Controllers\Api;

use Module\Auth\Http\Resources\UserResource;
use Illuminate\Http\Request;

class UserController
{
    /**
     * Handle a user request.
     *
     * @param Request $request
     * @return UserResource
     */
    public function __invoke(Request $request)
    {
        return new UserResource(
            $request->user()
        );
    }
}
