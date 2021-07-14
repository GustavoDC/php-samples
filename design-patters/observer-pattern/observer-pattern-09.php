<?php
/**
 * Hook system, for answering https://stackoverflow.com/questions/44004084/how-to-use-splobserver-for-hook-system
 * 
 * An adaptation of observer-pattern-07.php, this time using Traits and Closures.
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


// Then, let's declare the class that will represent the different observers.

    class MyObserver implements SplObserver
    {
        protected $closure;

        public function __construct(Closure $closure)
        {
            $this->closure = $closure->bindTo($this, $this);
        }

        public function update(SplSubject $subject, $name = null, $data = null)
        {
            $closure = $this->closure;
            $closure($subject, $name, $data);
        }
    }


    $user = new User;

    $function1 = function(SplSubject $subject, $name, $data) {
        echo $name . ": function1\n"; // we could also use $data here
    };

    $function2 = function(SplSubject $subject, $name, $data) {
        echo $name . ": function2\n";
    };

    $user->attach(new MyObserver($function1), 'all');
    $user->attach(new MyObserver($function2), 'User:created');

    $user->create();
    $user->update();

/* 

Output: 

User:created: function2
User:created: function1
User:updated: function1

*/