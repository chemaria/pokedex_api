<?php

namespace App\Shared\Application\Port;

/**
 * Port for database transaction management.
 * Defines the interface for executing operations within transactional context.
 */
interface TransactionManager
{
    /**
     * Executes an operation within a database transaction.
     *
     * @param  callable  $operation  The operation to execute
     * @return mixed The result of the operation
     *
     * @throws \Throwable If the operation fails, the transaction will be rolled back
     */
    public function execute(callable $operation): mixed;
}
