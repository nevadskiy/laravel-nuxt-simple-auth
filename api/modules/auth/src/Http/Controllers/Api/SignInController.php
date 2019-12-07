<?php

namespace Module\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Module\Auth\Http\Requests\SignInStoreRequest;
use Module\Auth\UseCases\SignIn\Handler;
use DomainException;
use Illuminate\Http\Response;
use Nevadskiy\Tokens\Exceptions\LockoutException;

class SignInController
{
    /**
     * Handle a sign in request.
     *
     * @param SignInStoreRequest $request
     * @param Handler $handler
     * @return Response|JsonResponse
     * @throws ValidationException
     */
    public function store(SignInStoreRequest $request, Handler $handler)
    {
        try {
            $user = $handler->handle($request->toCommand());
        } catch (LockoutException $e) {
            throw ValidationException::withMessages([
                'email' => __('auth::sign_in.throttle', ['seconds' => now()->diffInSeconds($e->getUnlockTime())])
            ])->status(Response::HTTP_TOO_MANY_REQUESTS);
        } catch (DomainException $e) {
            throw ValidationException::withMessages(['email' => __('auth::sign_in.failed')]);
        }

        return response()->json(['api_token' => $user->api_token]);
    }
}
