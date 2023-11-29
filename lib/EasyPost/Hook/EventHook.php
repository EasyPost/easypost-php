<?php

namespace EasyPost\Hook;

/**
 * The parent event that occurs when a hook is triggered.
 */
class EventHook
{
    private $eventHandlers = [];

    public function __invoke(...$args)
    {
        foreach ($this->eventHandlers as $eventHandler) {
            $eventHandler(...$args);
        }
    }

    /**
     * Add an HTTP handler to the list of handlers.
     *
     * @param callable $handler
     * @return EventHook
     */
    public function addHandler(callable $handler)
    {
        $this->eventHandlers[] = $handler;
        return $this;
    }

    /**
     * Remove an HTTP handler from the list of handlers.
     *
     * @param callable $handler
     * @return EventHook
     */
    public function removeHandler(callable $handler)
    {
        $index = array_search($handler, $this->eventHandlers, true);
        if ($index !== false) {
            array_splice($this->eventHandlers, $index, 1);
        }
        return $this;
    }
}
