<?php
/**
 * An adaptation of observer-pattern.03.php in order to answer:
 * https://stackoverflow.com/questions/8680715/splobserver-notification-issues
 * 
 * @author: Gustavo Cieslar
 */

class User implements \SplSubject
{
    private $observers = [];

    public function __construct()
    {
        $this->observers["all"] = [];
    }

    // The following methods represent the Observer management infrastructure:

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

    public function notify(string $name = "all", $data = null)
    {
        foreach ($this->getObservers($name) as $observer) {
            $observer->update($this, $name, $data);
        }
    }

    // The methods representing the business logic of the class:

    public function create()
    {
        // User creation code...

        $this->notify("User:created");
    }

    public function update()
    {
        // User update code...

        $this->notify("User:updated");
    }

    public function delete()
    {
        // User deletion code...

        $this->notify("User:deleted");
    }

}


/**
 * The logger classes
 */
class Logger1 implements \SplObserver
{
    public function update(\SplSubject $event, string $name = null, $data = null)
    {
        echo "Logger1: $name.\n"; // you could also log $data
    }
}

class Welcomer implements \SplObserver
{
    public function update(\SplSubject $event, string $name = null, $data = null)
    {
        echo "Welcomer: sending email.\n"; // here you could use the user name from $data
    }
}


/**
 * Test:
 */

$user = new User();
$user->attach(new Logger1(), "all");
$user->attach(new Welcomer(), "User:created");


$user->create();
$user->update();
$user->delete();

/* 

Output:

Welcomer: sending email.
Logger1: User:created.
Logger1: User:updated.
Logger1: User:deleted.

*/