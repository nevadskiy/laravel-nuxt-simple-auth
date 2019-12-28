<?php

namespace Module\Auth\Http\Controllers\Api;

use Module\Auth\Http\Resources\UserResource;
use Module\Auth\UseCases\SignUp\SignUpHandler;
use Module\Auth\UseCases\SignUp\SignUpRequest;

class SignUpController
{
    /**
     * Handle a sign up request.
     *
     * @param SignUpRequest $request
     * @param SignUpHandler $handler
     * @return UserResource
     */
    public function __invoke(SignUpRequest $request, SignUpHandler $handler)
    {
        return new UserResource(
            $handler->handle($request->toCommand())
        );
    }
}
