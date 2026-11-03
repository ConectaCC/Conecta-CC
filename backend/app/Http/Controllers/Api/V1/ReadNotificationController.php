<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class ReadNotificationController
{
    public function __invoke(Request $request, string $notification): Response
    {
        /** @var User $user */
        $user = $request->user();

        // só acha entre as do próprio usuário: a de outra pessoa dá 404
        $user->notifications()->whereKey($notification)->firstOrFail()->markAsRead();

        return response()->noContent();
    }
}
