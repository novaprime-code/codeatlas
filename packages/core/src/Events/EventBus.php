<?php

declare(strict_types=1);

namespace CodeAtlas\Core\Events;

/**
 * Synchronous, in-process event bus for pipeline hooks.
 *
 * Events are identified by string name (e.g. "scan.started"). Listeners fire
 * in registration order. Payload is arbitrary and passed by value.
 *
 * This bus is intentionally minimal — no wildcard subscriptions, no priorities,
 * no async. The pipeline uses it purely for observation, not control flow.
 */
final class EventBus
{
    /** @var array<string, list<callable(mixed): void>> */
    private array $listeners = [];

    /**
     * @param callable(mixed): void $handler
     */
    public function listen(string $event, callable $handler): void
    {
        $this->listeners[$event] ??= [];
        $this->listeners[$event][] = $handler;
    }

    public function dispatch(string $event, mixed $payload = null): void
    {
        foreach ($this->listeners[$event] ?? [] as $handler) {
            $handler($payload);
        }
    }

    public function forget(string $event): void
    {
        unset($this->listeners[$event]);
    }

    public function hasListeners(string $event): bool
    {
        return !empty($this->listeners[$event]);
    }
}
