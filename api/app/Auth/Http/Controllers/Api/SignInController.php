<?php

namespace App\Auth\Http\Controllers\Api;

use App\Auth\Http\Requests\SignInStoreRequest;
use App\Auth\UseCases\SignIn\Handler;
use App\Core\Http\Controllers\Controller;
use App\Core\Services\RateLimiter\LockoutException;
use DomainException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class SignInController extends Controller
{
    /**
     * SignInController constructor.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

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
        } catch (LockoutException $e) {
            throw ValidationException::withMessages([
                'email' => __('auth.throttle', ['seconds' => now()->diffInSeconds($e->getUnlockTime())])
            ])
                ->status(Response::HTTP_TOO_MANY_REQUESTS);
        } catch (DomainException $e) {
            throw ValidationException::withMessages(['email' => $e->getMessage()]);
        }

        return response()->json(['api_token' => $user->api_token]);
    }
}
