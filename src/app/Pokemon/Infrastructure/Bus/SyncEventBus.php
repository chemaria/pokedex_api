<?php

namespace App\Pokemon\Infrastructure\Bus;

use App\Pokemon\Domain\EventBus\EventBus;
use Illuminate\Support\Facades\Log;

final readonly class SyncEventBus implements EventBus
{
    public function dispatch(object $event): void
    {
        // En prod se podrian registrar listeners para manejar los eventos
        // Para esta prueba, logueo el evento
        $this->logEvent($event);
    }

    public function dispatchMultiple(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    private function logEvent(object $event): void
    {
        Log::info('Domain event dispatched', [
            'event_type' => get_class($event),
            'event_data' => method_exists($event, 'toArray') ? $event->toArray() : []
        ]);
    }
}
