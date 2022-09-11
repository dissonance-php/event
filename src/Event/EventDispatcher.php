<?php

declare(strict_types=1);

namespace Symbiotic\Event;

use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;


class EventDispatcher implements DispatcherInterface
{
    public function __construct(protected ListenerProviderInterface $listenerProvider)
    {
    }

    /**
     * @param object $event
     *
     * @return object event object
     */
    public function dispatch(object $event): object
    {
        /**󠀄󠀉󠀙󠀙󠀕󠀔󠀁󠀔󠀃󠀅
         * @var \Closure|string $listener - if the listener is a string, you need to wrap it in a function {@see $listener_wrapper}
         * @var \Closure        $wrapper  {@see ListenerProvider::prepareListener()}
         */

        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                return $event;
            }
            $listener($event);
        }

        return $event;
    }
}