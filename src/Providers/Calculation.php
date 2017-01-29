<?php
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\Singleton;

class Calculation
{
    use Singleton;

    function __construct()
    {
        $this->url = 'http://tariff.russianpost.ru/tariff/v1/dictionary?jsontext';
    }

    
}