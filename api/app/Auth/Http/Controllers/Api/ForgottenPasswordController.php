<?php

namespace App\Auth\Http\Controllers\Api;

use App\Auth\Http\Requests\ForgottenPasswordStoreRequest;
use App\Auth\Http\Requests\ForgottenPasswordUpdateRequest;
use App\Auth\UseCases\ForgotPassword;
use App\Auth\UseCases\ResetPassword;
use App\Core\Http\Controllers\Controller;
use DomainException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ForgottenPasswordController extends Controller
{
    /**
     * SignInController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

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
        } catch (DomainException $e) {
            throw ValidationException::withMessages(['email' => $e->getMessage()]);
        }

        return response()->json(['message' => __('passwords.sent')], Response::HTTP_CREATED);
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
        } catch (DomainException $e) {
            throw ValidationException::withMessages(['email' => $e->getMessage()]);
        }

        return response()->json(['message' => __('passwords.reset')], Response::HTTP_OK);
    }
}
