<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /**
     * Undocumented function
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $attr = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6'
        ]);

        Log::channel('user_logins')->info('login-data', [
            $request->all(), 
            'ip' => $request->getClientIp(), 
            'browser' => $request->header('User-Agent')
        ]);

        if (!Auth::guard('web')->attempt($attr, $request->remember)) {
            return $this->responseUnauthenticated('Credentials not match');
        }

        $user = auth()->user();
        if ($user) {
            $user->ip = $this->getIp();
            $user->last_login = Carbon::now();
            $user->save();
        }
        if (!$user->active) {
            return $this->responseForbidden("Nema pristup portalu.");
        }
        $planExpired = false;
        $message = null;

        /* if ($user->email !== 'it@actamedia.rs') {
            $user->tokens()->delete();
        } */
        $user->tokens()->delete();
        $authToken = $user->createToken(User::AUTH_TOKEN)->plainTextToken;

        return $this->responseSuccess([
            'user' => UserResource::make($user),
            'csrf_token' => csrf_token(),
            'auth_token' => $authToken,
            'plan_expired' => $planExpired,
            'message' => $message
        ]);
    }

    public function logout(Request $request)
    {
        //API token logout
        $request->user()->currentAccessToken()->delete();
        //SPA logout
        //auth()->logout();
        header('Clear-Site-Data: "cache", "executionContexts"');
        
        return $this->responseSuccess();
    }
}
