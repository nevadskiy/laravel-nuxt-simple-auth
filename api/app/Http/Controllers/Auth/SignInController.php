<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInStoreRequest;
use App\UseCases\Auth\SignIn\Handler;
use DomainException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SignInController extends Controller
{
    /**
     * Handle a sign in request.
     *
     * @param SignInStoreRequest $request
     * @param Handler $handler
     * @return Response|\Symfony\Component\HttpFoundation\Response
     * @throws ValidationException
     */
    public function store(SignInStoreRequest $request, Handler $handler)
    {
        try {
            $user = $handler->handle($request->toCommand());
        } catch (DomainException $e) {
            throw ValidationException::withMessages(['email' => $e->getMessage()]);
        }

        return response()->json(['api_token' => $user->api_token]);
    }
}
