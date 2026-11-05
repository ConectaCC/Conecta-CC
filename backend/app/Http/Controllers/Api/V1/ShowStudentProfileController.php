<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\StudentProfileResource;
use App\Models\User;

final class ShowStudentProfileController
{
    public function __invoke(User $student): StudentProfileResource
    {
        return new StudentProfileResource($student->load('disciplines'));
    }
}
