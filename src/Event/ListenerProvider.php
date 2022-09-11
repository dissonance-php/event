<?php

declare(strict_types=1);

namespace Symbiotic\Event;


class ListenerProvider implements ListenersInterface
{
    /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
     * @var \Closure|null
     * @see \Symbiotic\Core\Bootstrap\EventBootstrap::bootstrap()
     */
    protected ?\Closure $listenerWrapper;

    protected array $listeners = [];

    public function __construct(\Closure $listenerWrapper = null, array $listeners = [])
    {
        if ($listenerWrapper) {
            $this->setWrapper($listenerWrapper);
        }
        $this->listeners = $listeners;
    }

    /**
     * Sets the wrapper function for listeners
     *
     * @param \Closure $wrapper
     *
     * @return void
     */
    public function setWrapper(\Closure $wrapper): void
    {
        $this->listenerWrapper = $wrapper;
    }

    /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
     *
     * @param string          $event   the class name or an arbitrary event name
     *                                 (with an arbitrary name, you need a custom dispatcher not for PSR)
     *
     * @param \Closure|string $handler function or class name of the handler
     *                                 The event handler class must implement the handle method  (...$params) or
     *                                 __invoke(...$params)
     *                                 <Important:> When adding listeners as class names, you will need to adapt them
     *                                 to \Closure when you return them in the getListenersForEvent() method!!!
     *
     * @return void
     */
    public function add(string $event, \Closure|string $handler): void
    {
        $this->listeners[$event][] = $handler;
    }

    /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
     *
     * @param object $event
     *
     * @return iterable|\Closure[]
     */
    public function getListenersForEvent(object $event): iterable
    {
        $parents = \class_parents($event);
        $implements = \class_implements($event);
        $classes = array_merge([\get_class($event)], $parents ?: [], $implements ?: []);
        $listeners = [];
        foreach ($classes as $v) {
            $listeners = array_merge($listeners, $this->listeners[$v] ?? []);
        }
        $wrapper = $this->listenerWrapper;

        return $wrapper ? array_map(function ($item) use ($wrapper) {
            return $wrapper($item);
        }, $listeners) : $listeners;
    }
}