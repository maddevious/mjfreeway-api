<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use App\Repositories\OutputRepository;
use Log;

class AuthenticateWithBasicAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $output = new OutputRepository;
        $errors = [];

        return Auth::onceBasic() ?: $next($request);

        if (config('auth.authorized_identities')->contains($request->getUser(),$request->getPassword())) {
            return $next($request);
        } else {
            $errors[] = "Invalid basic auth credentials; headers: ".json_encode($request->header());
        }

        Log::error(implode(', ', $errors));
        return $output->setMessages(402)->render();
    }
}
