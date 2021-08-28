# Symbiotic Event Dispatcher
README.RU.md  [РУССКОЕ ОПИСАНИЕ](https://github.com/symbiotic-php/event/blob/master/README.RU.md)
## Features

- Compatible with PSR-14
- Simple and lightweight (2.5 Kb)
- Accepts class names as subscribers
- Can be shared with your DI container
- No private properties and methods

## Installation
```
composer require symbiotic/event 
```

## Usage
##### Basic
```php
use Symbiotic\Event\ListenerProvider;
use Symbiotic\Event\EventDispatcher;

$listeners  = new ListenerProvider();
$dispatcher = new EventDispatcher($listeners);

$listeners->add(\MyEvents\FirstEvent::class, function(\MyEvents\FirstEvent $event) {
    // handle event
});

// Run event

$event = new \MyEvents\FirstEvent();
$dispatcher->dispatch(new \MyEvents\FirstEvent());

```
##### With your revolver subscribers
You can wrap the subscribers yourself and handle the event
 for example, you can pass the event to a class or execute it through your DI container.

```php
use Symbiotic\Event\ListenerProvider;
use Symbiotic\Event\EventDispatcher;

/**
 * @var \Closure|string $listener  you can wrap the subscribers yourself and handle the event,
 * for example, you can pass the event to a class or execute it through your DI container
 **/
$listener_wrapper = function($listener) {
    return function(object $event) use ($listener) {
            // if classname
            if(is_string($listener) && class_exists($listener)) {
                $listener = new $listener();
                return $listener->handle($event);
            } elseif(is_callable($listener)) {
                return $listener($event);
            }
    };
};

$listeners  = new ListenerProvider($listener_wrapper);
$dispatcher = new EventDispatcher($listeners);
// classname handler
$listeners->add(\MyEvents\FirstEvent::class, \MyEvents\Handlers\FirstHandler::class);
// callable handler
$listeners->add(\MyEvents\FirstEvent::class, function(\MyEvents\FirstEvent $event) {
    // handle event
});

// Run event

$event = new \MyEvents\FirstEvent();
$dispatcher->dispatch(new \MyEvents\FirstEvent());

```


## Stoppable events
If the event implements the \Psr\EventDispatcher\StoppableEventInterface interface, then it can be stopped:
```php
class StopEvent implements Psr\EventDispatcher\StoppableEventInterface
{
    // Your logic for stopping
    public function isPropagationStopped(): bool
    {
        return true;
    }
}
```
This behavior can be useful for events that require listeners to stop processing.


