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

    public function addHandler($handler)
    {
        $this->eventHandlers[] = $handler;
        return $this;
    }

    public function removeHandler($handler)
    {
        $index = array_search($handler, $this->eventHandlers, true);
        if ($index !== false) {
            array_splice($this->eventHandlers, $index, 1);
        }
        return $this;
    }
}
