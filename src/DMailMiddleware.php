<?php

namespace FutureGadgetLab\PhoneWave;

use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class DMailMiddleware
{
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        $mails = Arr::wrap(\config('phone-wave.d-mail') ?? []);

        if (!count($mails)) {
            return $response;
        }

        $mail = Arr::random($mails);
        
        if ($mail) {
            $response->header('D-Mail-Content', $mail);
        }
        
        return $response;
    }
}
