<?php

namespace Module\Auth\Http\Controllers\Api;

use Module\Auth\UseCases\SignOut\SignOutHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SignOutController
{
    /**
     * Handle a sign out request.
     *
     * @param Request $request
     * @param SignOutHandler $handler
     * @return Response|JsonResponse
     */
    public function __invoke(Request $request, SignOutHandler $handler)
    {
        $handler->handle($request->user());

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
