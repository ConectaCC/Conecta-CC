<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;

final class CurrentTermsController
{
    public function __invoke(): JsonResponse
    {
        $version = config()->string('conectacc.terms_version');
        // objeto ou array viram o mesmo JSON na resposta
        $terms = json_decode(File::get(resource_path("terms/{$version}.json")), true, flags: JSON_THROW_ON_ERROR); // @pest-mutate-ignore: TrueToFalse

        return response()->json($terms);
    }
}
