<?php

namespace App\Shared\Application\Middleware;

use App\Shared\Application\Port\TransactionManager;
use Closure;

/**
 * Middleware to wrap command handling in a database transaction.
 * This middleware can be used across multiple features to ensure transactional consistency.
 */
final readonly class TransactionalCommandMiddleware
{
    public function __construct(
        private TransactionManager $transactionManager
    ) {}

    /**
     * Handle a command within a transaction.
     *
     * @param  object  $command  The command to execute
     * @param  Closure  $next  The next middleware in the chain
     * @return mixed The result of the command
     */
    public function handle(object $command, Closure $next): mixed
    {
        return $this->transactionManager->execute(
            fn () => $next($command)
        );
    }

    /**
     * Execute an operation directly within a transaction.
     *
     * @param  callable  $operation  The operation to execute
     * @return mixed The result of the operation
     */
    public function execute(callable $operation): mixed
    {
        return $this->transactionManager->execute($operation);
    }
}
