<?php
/**
 * An adaptation of observer-pattern.03.php in order to answer:
 * https://stackoverflow.com/questions/8680715/splobserver-notification-issues
 * 
 * @author: Gustavo Cieslar
 */

class Event implements \SplSubject
{
    private $observers = [];

    public function __construct()
    {
        $this->observers["all"] = [];
    }

    private function initObserversGroup(string $name = "all"): void
    {
        if (!isset($this->observers[$name])) {
            $this->observers[$name] = [];
        }
    }

    private function getObservers(string $name = "all")
    {
        $this->initObserversGroup($name);
        $group = $this->observers[$name];
        $all = $this->observers["all"];

        return array_merge($group, $all);
    }

    public function attach(\SplObserver $observer, string $name = "all")
    {
        $this->initObserversGroup($name);
        $this->observers[$name][] = $observer;
    }

    public function detach(\SplObserver $observer, string $name = "all")
    {
        foreach ($this->getObservers($name) as $key => $o) {
            if ($o === $observer) {
                unset($this->observers[$name][$key]);
            }
        }
    }

    public function notify(string $name = "all")
    {
        foreach ($this->getObservers($name) as $observer) {
            $observer->update($this, $name);
        }
    }

    // The methods representing the business logic of the class:

    public function eventMethod1()
    {
        // some code here...

        $this->notify("action1");
    }

    public function eventMethod2()
    {
        // some code here...

        $this->notify("action2");
    }

    public function eventMethod3()
    {
        // some code here...

        $this->notify("action3");
    }

}


/**
 * This Concrete Component logs any events it's subscribed to.
 */
class Balabala1 implements \SplObserver
{
    public function update(\SplSubject $event, string $name = null)
    {
        echo "$name: Balabala1.\n";
    }
}
class Balabala2 implements \SplObserver
{
    public function update(\SplSubject $event, string $name = null)
    {
        echo "$name: Balabala2.\n";
    }
}
class Balabala3 implements \SplObserver
{
    public function update(\SplSubject $event, string $name = null)
    {
        echo "$name: Balabala3.\n";
    }
}


/**
 * Test:
 */

$event = new Event();
$event->attach(new Balabala1(), "action1");
$event->attach(new Balabala2(), "action1");
// $event->attach(new Balabala3(), "all");


$event->eventMethod1();
$event->eventMethod2();
$event->eventMethod3();


