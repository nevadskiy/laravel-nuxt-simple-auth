<?php

namespace Module\Auth\Http\Controllers\Api;

use Illuminate\Validation\ValidationException;
use Module\Auth\Http\Requests\ForgottenPasswordStoreRequest;
use Module\Auth\Http\Requests\ForgottenPasswordUpdateRequest;
use Module\Auth\UseCases\ForgotPassword;
use Module\Auth\UseCases\ResetPassword;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Nevadskiy\Tokens\Exceptions\LockoutException;
use Nevadskiy\Tokens\Exceptions\TokenException;

class ForgottenPasswordController
{
    /**
     * Handle a forgotten password store request.
     *
     * @param ForgottenPasswordStoreRequest $request
     * @param ForgotPassword\Handler $handler
     * @return JsonResponse|Response
     * @throws ValidationException
     */
    public function store(ForgottenPasswordStoreRequest $request, ForgotPassword\Handler $handler)
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
     * @param ForgottenPasswordUpdateRequest $request
     * @param ResetPassword\Handler $handler
     * @return JsonResponse|Response
     * @throws ValidationException
     */
    public function update(ForgottenPasswordUpdateRequest $request, ResetPassword\Handler $handler)
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
