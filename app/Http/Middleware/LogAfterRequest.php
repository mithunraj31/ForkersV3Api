<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogAfterRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $requestMsg = $this->getRequestMessage($request);
        Log::info("Start $requestMsg", (array)  $request->all());
        return $next($request);
    }

    public function terminate($request, $response)
	{
        $requestMsg = $this->getRequestMessage($request);
        $statusCode = $response->getStatusCode();

        Log::info("Finished $requestMsg, Response $statusCode", [
            'request' => $request->all(),
            'response' => $response->getContent()
        ]);
    }

    private function getRequestMessage($request)
    {
        $requestMethod = $request->getMethod();
        $requestUri = $request->getRequestUri();
        return "Request $requestMethod $requestUri";
    }
}
