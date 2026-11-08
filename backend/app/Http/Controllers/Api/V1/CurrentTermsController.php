<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

final class CurrentTermsController
{
    public function __invoke(): JsonResponse
    {
        $version = config()->string('conectacc.terms_version');
        $terms = json_decode(File::get(resource_path("terms/{$version}.json")), true, flags: JSON_THROW_ON_ERROR);

        return response()->json($terms);
    }
}
