<?php
/**
 * An refactorization of observer-pattern.06.php (using Traits) in order to answer:
 * https://stackoverflow.com/questions/8680715/splobserver-notification-issues
 * 
 * @author: Gustavo Cieslar
 */

    trait SubjectTrait {

        private $observers = [];

        // this is not a real __construct() (we will call it later)
        public function construct()
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
    }


    class User implements \SplSubject
    {

        use SubjectTrait {
            // It's necessary to alias construct() because it would conflict with other methods.
            SubjectTrait::construct as protected constructSubject;
        }

        public function __construct()
        {
            $this->constructSubject();
        }


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
            // you could also log $data
            echo "Logger1: $name.\n";
        }
    }

    class Logger2 implements \SplObserver
    {
        public function update(\SplSubject $event, string $name = null, $data = null)
        {
            // you could also log $data
            echo "Logger2: $name.\n"; 
        }
    }

    class Welcomer implements \SplObserver
    {
        public function update(\SplSubject $event, string $name = null, $data = null)
        {
            // here you could use the user name from $data
            echo "Welcomer: sending email.\n";
        }
    }


/**
 * The client code.
 */

    // create a User object
    $user = new User();

    // subscribe the logger 1 to all user events
    $user->attach(new Logger1(), "all");

    // subscribe the logger 2 only to user deletions
    $user->attach(new Logger2(), "User:deleted");

    // subscribe the welcomer emailer only to user creations
    $user->attach(new Welcomer(), "User:created");

    // perform some actions
    $user->create();
    $user->update();
    $user->delete();

    // Output:

    // Welcomer: sending email.
    // Logger1: User:created.
    // Logger1: User:updated.
    // Logger2: User:deleted.
    // Logger1: User:deleted.