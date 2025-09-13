<?php

namespace App\Pokemon\Application\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Middleware to wrap command handling in a database transaction.
 */
final readonly class TransactionalCommandMiddleware
{
    public function handle(object $command, Closure $next): mixed
    {
        return DB::transaction(function () use ($command, $next) {
            return $next($command);
        });
    }

    public function execute(callable $operation): mixed
    {
        return DB::transaction(function () use ($operation) {
            try {
                return $operation();
            } catch (Throwable $exception) {
                throw $exception;
            }
        });
    }
}