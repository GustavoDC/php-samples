<?php
/**
 * Strategy pattern
 * @author: Gustavo Cieslar
 */

interface DisplayBehaviour 
{
    public function display();
}

class PrintTextDisplayBehaviour implements DisplayBehaviour
{
    public function display()
    {
        echo "Imprimo un texto\n";
    }
}

class PlayButtonDisplayBehaviour implements DisplayBehaviour
{
    public function display()
    {
        echo "Muestro un botón de play\n";
    }
}

abstract class Excercise
{
    public DisplayBehaviour $displayBehaviour;

    public function remove()
    {
        echo "Elimino el ejercicio\n";
    }
    
    public function doDisplay()
    {
        $this->displayBehaviour->display();
    }
    
}

class TranslateExcercise extends Excercise
{
    function __construct()
    {
        $displayBehaviour = new PrintTextDisplayBehaviour();
        $this->displayBehaviour = $displayBehaviour;
    }
}

class ListenExcercise extends Excercise
{
    function __construct()
    {
        $this->displayBehaviour = new PlayButtonDisplayBehaviour();
    }
}

$translationExcercise = new TranslateExcercise();
$translationExcercise->doDisplay();

$listeningExcercise = new ListenExcercise();
$listeningExcercise->doDisplay();

/* 

Output:

Imprimo un texto
Muestro un botón de play

*/
