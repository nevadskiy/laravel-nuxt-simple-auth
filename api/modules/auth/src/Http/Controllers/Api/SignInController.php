<?php

namespace Module\Auth\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Module\Auth\UseCases\SignIn\SignInHandler;
use DomainException;
use Illuminate\Http\Response;
use Module\Auth\UseCases\SignIn\SignInRequest;
use Nevadskiy\Tokens\Exceptions\LockoutException;

class SignInController
{
    /**
     * Handle a sign in request.
     *
     * @param SignInRequest $request
     * @param SignInHandler $handler
     * @return JsonResponse
     * @throws ValidationException
     */
    public function __invoke(SignInRequest $request, SignInHandler $handler)
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
