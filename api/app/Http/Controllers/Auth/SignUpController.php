<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpStoreRequest;
use App\Http\Resources\UserResource;
use App\UseCases\Auth\SignUp\Handler;
use Symfony\Component\HttpFoundation\Response;

class SignUpController extends Controller
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
