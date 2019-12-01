<?php

namespace App\Auth\Http\Controllers\Api;

use App\Auth\Http\Requests\SignUpStoreRequest;
use App\Auth\Http\Resources\UserResource;
use App\Auth\UseCases\SignUp\Handler;
use App\Core\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class SignUpController extends Controller
{
    /**
     * SignUpController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

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
