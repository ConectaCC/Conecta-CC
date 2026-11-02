<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\NotificationResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ListNotificationsController
{
    public function __invoke(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return response()->json([
            'data' => NotificationResource::collection($user->notifications()->latest()->limit(20)->get()),
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }
}
