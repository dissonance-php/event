# Dissonance Event Dispatcher

## Характеристики

- Совместим с PSR-14
- Не имеет зависимостей и очень легкий (2.5 Kb)
- Возможно использовать классы в качестве подписчиков
- Можно совместно использовать с контейнером DI
- Нет приватных свойств и методов

## Installation
```
composer require dissonance/event 
```

## Использование
##### Базовое
```php
use Dissonance\Event\ListenerProvider;
use Dissonance\Event\EventDispatcher;

$listeners  = new ListenerProvider();
$dispatcher = new EventDispatcher($listeners);

$listeners->add(\MyEvents\FirstEvent::class, function(\MyEvents\FirstEvent $event) {
    // handle event
});

// Run event

$event = new \MyEvents\FirstEvent();
$dispatcher->dispatch(new \MyEvents\FirstEvent());

```
##### С резолвером подписчиков или через ваш DI контейнер
Вы сами можете обернуть подписчиков и обработать событие, например можете передать событие классу или выполнить через ваш DI контейнер.


```php
use Dissonance\Event\ListenerProvider;
use Dissonance\Event\EventDispatcher;

/**
 * @var \Closure|string $listener 
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

// string classname handler
$listeners->add(\MyEvents\FirstEvent::class, \MyEvents\Handlers\FirstHandler::class);
// callable handler
$listeners->add(\MyEvents\FirstEvent::class, function(\MyEvents\FirstEvent $event) {
    // handle event
});

// Run event
$event = new \MyEvents\FirstEvent();
$dispatcher->dispatch(new \MyEvents\FirstEvent());

```



## Останавливаемые события
Если событие реализует интерфейс \Psr\EventDispatcher\StoppableEventInterface, то его можно остановить
```php
 class StopEvent implements Psr\EventDispatcher\StoppableEventInterface
{
    // ...

    public function isPropagationStopped(): bool
    {
        return true;
    }
}
```
Такое поведение может быть полезным для событий, в которых требуется прекратить обработку прослушивателями.



