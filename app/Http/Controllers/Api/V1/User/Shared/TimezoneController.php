<?php

namespace App\Http\Controllers\Api\V1\User\Shared;

use App\Http\Controllers\Controller;
use DateTimeZone;

class TimezoneController extends Controller
{
    public function getTimeZones()
    {
        $timeZones = DateTimeZone::listIdentifiers();

        return response()->json($timeZones);
    }
}
