<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReportErrorController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function reportError(Request $request)
    {
        $user = auth()->user();
        $errorData = $request->all();
        $userName = "Guest";
        if ($user) {
            $userName = $user->name;
            $errorData['user_name'] = $user->name;
            $errorData['user_email'] = $user->email;
            $errorData['user_id'] = $user->id;
            $errorData['ip'] = $request->getClientIp();
        }
        Log::channel('fe_app_errors')->info($userName, $errorData);
    }

}
