<?php
/**
 * Observer Pattern
 * 
 * Adapted from Java to PHP, from the Book: Head First Design Patterns.
 * Without usage of SplObserver and SplSubject interfaces.
 * 
 * A weather station + weather data object (subject) + display elements (observers) sample.
 * 
 */


/** 
 * Subject Interface
 */
interface Subject
{
    public function registerObserver(Observer $o);

    public function removeObserver(Observer $o);

    public function notifyObservers();
}

/** 
 * Observer Interface
 */
interface Observer
{
    public function update();
}

/** 
 * Display Element Interface
 */
interface DisplayElement
{
    public function display();
}

/** 
 * Weather data class (Subject)
 * 
 * @property SplObjectStorage $observers
 * @property int $temperature
 * @property int $humidity
 */
class WeatherData implements Subject
{
    private SplObjectStorage $observers;
    private int $temperature;
    private int $humidiy;

    public function __construct()
    {
        $this->observers = new SplObjectStorage();
    }

    public function registerObserver(Observer $o)
    {
        $this->observers->attach($o);
    }

    public function removeObserver(Observer $o)
    {
        $this->observers->detach($o);
    }

    public function notifyObservers()
    {
        foreach ($this->observers as $o)
        {
            $o->update();
        }
    }

    public function getTemperature()
    {
        return $this->temperature;
    }

    public function getHumidiy()
    {
        return $this->humidiy;
    }

    public function meassurementsChanged()
    {
        $this->notifyObservers();
    }

    public function setMeassurements(int $temperature, int $humidiy)
    {
        $this->temperature = $temperature;
        $this->humidiy = $humidiy;
        $this->meassurementsChanged();
    }
}

/** 
 * Current Conditions Display (Observer)
 * 
 * @property int $temperature
 * @property int $humidity
 * @property WeatherData $weatherData
 */
class CurrentConditionsDisplay implements Observer, DisplayElement
{
    private int $temperature;
    private int $humidity;
    private WeatherData $weatherData;

    public function __construct(WeatherData $weatherData)
    {
        $this->weatherData = $weatherData;
    }

    public function update()
    {
        $this->temperature = $this->weatherData->getTemperature();
        $this->humidity = $this->weatherData->getHumidiy();
        $this->display();
    }

    public function display()
    {
        echo "CURRENT CONDITIONS:\n";
        echo "Temperature: " . $this->temperature . "\n";
        echo "Humidity: " . $this->humidity . "\n";
    }
}

/** 
 * Statistics display (Observer)
 * 
 * @property float $maxTemp
 * @property float $minTemp
 * @property float $tempSum
 * @property int $numReadings
 * @property WeatherData $weatherData
 */
class StatisticsDisplay implements Observer, DisplayElement
{
    private $maxTemp = 0.0;
    private $minTemp = 1000;
    private $tempSum = 0.0;
    private $numReadings = 0;
    private $weatherData;

    public function __construct(WeatherData $weatherData)
    {
        $this->weatherData = $weatherData;
    }

    public function update()
    {
        $temp = $this->weatherData->getTemperature();

        $this->tempSum += $temp;
        $this->numReadings++;

        if ($temp > $this->maxTemp) {
            $this->maxTemp = $temp;
        }

        if ($temp < $this->minTemp) {
            $this->minTemp = $temp;
        }

        $this->display();
    }

    public function display()
    {
        echo "STATISTICS:\n";
        echo "Max. temp.: " . $this->maxTemp . "\n";
        echo "Min. temp: " . $this->minTemp . "\n";
        echo "Avg. temp: " . $this->tempSum / $this->numReadings . "\n";
    }
}


/** 
 * Weather Station
 * 
 * @property WeatherData $weatherData
 * @property DisplayElement $currentDisplay;
 * @property DisplayElement $statisticsDisplay;
 */
class WeatherStation {

    private $weatherData;
    private $currentDisplay;
    private $statisticsDisplay;

    public function __construct()
    {
        $this->weatherData = new WeatherData();
        $this->currentDisplay = new CurrentConditionsDisplay($this->weatherData);
        $this->statisticsDisplay = new StatisticsDisplay($this->weatherData);

        $this->weatherData->registerObserver($this->currentDisplay);
        $this->weatherData->registerObserver($this->statisticsDisplay);

        $this->weatherData->setMeassurements(32, 75);
        $this->weatherData->setMeassurements(35, 80);
    }
}

$station = new WeatherStation();

/* 

Output:

CURRENT CONDITIONS:
Temperature: 32
Humidity: 75
STATISTICS:
Max. temp.: 32
Min. temp: 32
Avg. temp: 32
CURRENT CONDITIONS:
Temperature: 35
Humidity: 80
STATISTICS:
Max. temp.: 35
Min. temp: 32
Avg. temp: 33.5

*/
