<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cast integer identifiers in API JSON responses to strings.
 *
 * Applies to keys named "id", ending with "_id", or ending with "_ids" (arrays).
 */
class CastIdsToString
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $data = $response->getData(true);

            if (is_array($data)) {
                $response->setData($this->transform($data));
            }
        }

        return $response;
    }

    private function transform(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = is_string($key) && $this->isIdsKey($key)
                    ? array_map(fn ($v) => is_int($v) ? (string) $v : $v, $this->transform($value))
                    : $this->transform($value);
            } elseif (is_int($value) && is_string($key) && $this->isIdKey($key)) {
                $data[$key] = (string) $value;
            }
        }

        return $data;
    }

    private function isIdKey(string $key): bool
    {
        return $key === 'id' || str_ends_with($key, '_id');
    }

    private function isIdsKey(string $key): bool
    {
        return $key === 'ids' || str_ends_with($key, '_ids');
    }
}
