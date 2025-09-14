<?php

namespace App\Shared\Infrastructure\Persistence\Eloquent;

use App\Shared\Application\Port\TransactionManager;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * TransactionManager adapter using Eloquent/Laravel Database.
 * Encapsulates Laravel transaction handling behind an interface.
 */
final readonly class EloquentTransactionManager implements TransactionManager
{
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
