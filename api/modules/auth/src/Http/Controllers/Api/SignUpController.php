<?php

namespace Module\Auth\Http\Controllers\Api;

use Module\Auth\Http\Requests\SignUpStoreRequest;
use Module\Auth\Http\Resources\UserResource;
use Module\Auth\UseCases\SignUp\Handler;
use Symfony\Component\HttpFoundation\Response;

class SignUpController
{
    /**
     * Handle a sign up request.
     *
     * @param SignUpStoreRequest $request
     * @param Handler $handler
     * @return Response|UserResource
     */
    public function store(SignUpStoreRequest $request, Handler $handler)
    {
        return new UserResource(
            $handler->handle($request->toCommand())
        );
    }
}
