<?php

namespace App\Http\Middleware;

use App\Models\LogActivity;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogActivityMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        if (auth()->check()) {
            $event = $this->getEventName($request);

            if ($event && $this->shouldLog($request)) {
                LogActivity::create([
                    'user_id' => auth()->id(),
                    'event' => $event,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'description' => $this->getDescription($request, $event),
                ]);
            }
        }
    }

    protected function getEventName(Request $request): ?string
    {
        $method = $request->method();
        $path = $request->path();

        if (str_contains($path, 'admin')) {
            return match($method) {
                'POST' => 'admin_create',
                'PUT', 'PATCH' => 'admin_update',
                'DELETE' => 'admin_delete',
                default => 'admin_view',
            };
        }

        if (str_contains($path, 'order') || str_contains($path, 'checkout')) {
            return 'create_order';
        }

        if (str_contains($path, 'balance')) {
            return 'balance_activity';
        }

        return null;
    }

    protected function shouldLog(Request $request): bool
    {
        $excludePaths = [
            'sanctum/csrf-cookie',
            'api/health',
        ];

        foreach ($excludePaths as $path) {
            if (str_contains($request->path(), $path)) {
                return false;
            }
        }

        return true;
    }

    protected function getDescription(Request $request, string $event): string
    {
        return match($event) {
            'admin_create' => 'Created new resource',
            'admin_update' => 'Updated resource',
            'admin_delete' => 'Deleted resource',
            'create_order' => 'Created new order',
            'balance_activity' => 'Balance activity',
            default => 'Accessed ' . $request->path(),
        };
    }
}
