<?php
/** 
 * Observer pattern using Closures
 * Source: https://riptutorial.com/php/example/8748/use-closures-to-implement-observer-pattern
 */

// Let's first declare a class whose purpose is to notify observers when its property is changed.

class ObservedStuff implements SplSubject
{
    protected $property;
    protected $observers = [];

    public function attach(SplObserver $observer)
    {
        $this->observers[] = $observer;
        return $this;
    }

    public function detach(SplObserver $observer)
    {
        if (false !== $key = array_search($observer, $this->observers, true)) {
            unset($this->observers[$key]);
        }
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function setProperty($property)
    {
        $this->property = $property;
        $this->notify();
    }
}

// Then, let's declare the class that will represent the different observers.

class MyObserver implements SplObserver
{
    protected $name;
    protected $closure;

    public function __construct(Closure $closure, $name)
    {
        $this->closure = $closure->bindTo($this, $this);
        $this->name = $name;
    }

    public function update(SplSubject $subject)
    {
        $closure = $this->closure;
        $closure($subject);
    }
}

$o = new ObservedStuff;

$observer1 = function(SplSubject $subject) {
    echo $this->name, ' has been notified! New property value: ', $subject->getProperty(), "\n";
};

$observer2 = function(SplSubject $subject) {
    echo $this->name, ' has been notified! New property value: ', $subject->getProperty(), "\n";
};

$o->attach(new MyObserver($observer1, 'Observer1'))
  ->attach(new MyObserver($observer2, 'Observer2'));

$o->setProperty('Hello world!');

// Output:
// Observer1 has been notified! New property value: Hello world!
// Observer2 has been notified! New property value: Hello world!
