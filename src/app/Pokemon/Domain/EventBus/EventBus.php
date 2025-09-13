<?php

namespace App\Pokemon\Domain\EventBus;

interface EventBus
{
    public function dispatch(object $event): void;

    public function dispatchMultiple(array $events): void;
}