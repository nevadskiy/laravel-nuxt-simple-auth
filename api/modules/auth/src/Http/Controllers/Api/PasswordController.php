<?php

namespace Module\Auth\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;
use Module\Auth\UseCases\PasswordForgot\PasswordForgotHandler;
use Module\Auth\UseCases\PasswordForgot\PasswordForgotRequest;
use Module\Auth\UseCases\PasswordReset\PasswordResetHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Module\Auth\UseCases\PasswordReset\PasswordResetRequest;
use Nevadskiy\Tokens\Exceptions\LockoutException;
use Nevadskiy\Tokens\Exceptions\TokenException;

class PasswordController
{
    /**
     * Handle a forgotten password store request.
     *
     * @param PasswordForgotRequest $request
     * @param PasswordForgotHandler $handler
     * @return JsonResponse|Response
     * @throws ValidationException
     */
    public function forgot(PasswordForgotRequest $request, PasswordForgotHandler $handler)
    {
        try {
            $handler->handle($request->toCommand());
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages(['email' => __('auth::passwords.not_found')]);
        } catch (LockoutException $e) {
            throw ValidationException::withMessages(['email' => __('auth::passwords.throttle')])
                ->status(Response::HTTP_TOO_MANY_REQUESTS);
        }

        return response()->json(['message' => __('auth::passwords.sent')], Response::HTTP_CREATED);
    }

    /**
     * Handle a forgotten password update request.
     *
     * @param PasswordResetRequest $request
     * @param PasswordResetHandler $handler
     * @return JsonResponse|Response
     * @throws ValidationException
     */
    public function reset(PasswordResetRequest $request, PasswordResetHandler $handler)
    {
        try {
            $handler->handle($request->toCommand());
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages(['email' => __('auth::passwords.not_found')]);
        } catch (LockoutException $e) {
            throw ValidationException::withMessages(['email' => __('auth::passwords.throttle')])
                ->status(Response::HTTP_TOO_MANY_REQUESTS);
        } catch (TokenException $e) {
            throw ValidationException::withMessages(['token' => __('auth::passwords.invalid_token')]);
        }

        return response()->json(['message' => __('auth::passwords.success')], Response::HTTP_OK);
    }
}
