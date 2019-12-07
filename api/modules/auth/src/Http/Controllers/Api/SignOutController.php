<?php

namespace Module\Auth\Http\Controllers\Api;

use Module\Auth\UseCases\SignOut\Handler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SignOutController
{
    /**
     * Handle a sign out request.
     *
     * @param Request $request
     * @param Handler $handler
     * @return Response|JsonResponse
     */
    public function destroy(Request $request, Handler $handler)
    {
        $handler->handle($request->user());

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
