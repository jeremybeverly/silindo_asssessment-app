<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Wraps JSON API responses in the shared envelope:
 * { success, code, message, data }.
 *
 * The office dashboard already produces this shape for its own API, so using it
 * here keeps a single contract for the integration client.
 */
class EnsureJsonEnvelope
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $response instanceof JsonResponse) {
            return $response;
        }

        $payload = $response->getData(true);

        if (is_array($payload) && array_key_exists('success', $payload)) {
            return $response;
        }

        $status = $response->getStatusCode();

        $response->setData([
            'success' => $response->isSuccessful(),
            'code' => $status,
            'message' => $response->isSuccessful()
                ? 'OK'
                : (Response::$statusTexts[$status] ?? 'Error'),
            'data' => $status >= 500 ? null : $payload,
        ]);

        return $response;
    }
}
