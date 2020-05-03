<?php
namespace LapayGroup\RussianPost\Entity;

use LapayGroup\RussianPost\Enum\AddressType;

/**
 * Адрес для услуги Легкий возврат
 *
 * Class AddressReturn
 * @package LapayGroup\RussianPost\Entity
 */
Class AddressReturn
{
    /** @var string  */
    private $address_type = AddressType::DEFAULT; // Тип адреса
    /** @var string|null  */
    private $area = null; // Район
    /** @var string|null  */
    private $building = null; // Часть здания: Строение
    /** @var string|null  */
    private $corpus = null; // Часть здания: Корпус
    /** @var string|null  */
    private $hotel = null; // Название гостиницы
    /** @var string|null  */
    private $house = null; // Часть адреса: Номер здания
    /** @var string  */
    private $index = null; // Почтовый индекс (ОБЯЗ!)
    /** @var string|null  */
    private $letter = null; // Часть здания: Литера
    /** @var string|null  */
    private $location = null; // Микрорайон
    /** @var boolean|null  */
    private $manual_address_input = null; // Признак ручного ввода адреса
    /** @var string|null  */
    private $num_address_type = null; // Номер для а/я, войсковая часть, войсковая часть ЮЯ, полевая почта
    /** @var string|null  */
    private $office = null; // Часть здания: Офис
    /** @var string  */
    private $place = null; // Населенный пункт (ОБЯЗ!)
    /** @var string  */
    private $region = null; // Область, регион (ОБЯЗ!)
    /** @var string|null  */
    private $room = null; // Часть здания: Номер помещения
    /** @var string|null  */
    private $slash = null; // Часть здания: Дробь
    /** @var string|null  */
    private $street = null; // Часть адреса: Улица
    /** @var string|null  */
    private $vladenie = null; // Часть адреса: Владение

    /**
     * Возвращает массив параметров адреса для запроса
     * @return array
     */
    public function asArr()
    {
        if (empty($this->index))
            throw new \InvalidArgumentException('Не передан почтовый индекс!');

        if (empty($this->place))
            throw new \InvalidArgumentException('Не передан населенный пункт!');

        if (empty($this->region))
            throw new \InvalidArgumentException('Не передан регион (область)!');

        $params = [];
        $params['address-type'] = $this->getAddressType();
        if (!is_null($this->area))
            $params['area'] = $this->area;

        if (!is_null($this->building))
            $params['building'] = $this->building;

        if (!is_null($this->corpus))
            $params['corpus'] = $this->corpus;

        if (!is_null($this->hotel))
            $params['hotel'] = $this->hotel;

        if (!is_null($this->house))
            $params['house'] = $this->house;

        $params['index'] = $this->index;

        if (!is_null($this->house))
            $params['house'] = $this->house;

        if (!is_null($this->letter))
            $params['letter'] = $this->letter;

        if (!is_null($this->location))
            $params['location'] = $this->location;

        if (!is_null($this->manual_address_input))
            $params['manual-address-input'] = $this->manual_address_input;

        if (!is_null($this->num_address_type))
            $params['num-address-type'] = $this->num_address_type;

        if (!is_null($this->office))
            $params['office'] = $this->office;

        $params['place'] = $this->place;
        $params['region'] = $this->region;

        if (!is_null($this->room))
            $params['room'] = $this->room;

        if (!is_null($this->slash))
            $params['slash'] = $this->slash;

        if (!is_null($this->street))
            $params['street'] = $this->street;

        if (!is_null($this->vladenie))
            $params['vladenie'] = $this->vladenie;

        return $params;
    }

    /**
     * @return string
     */
    public function getAddressType()
    {
        return $this->address_type;
    }

    /**
     * @param string $address_type
     */
    public function setAddressType($address_type)
    {
        $this->address_type = $address_type;
    }

    /**
     * @return string|null
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * @param string|null $area
     */
    public function setArea($area)
    {
        $this->area = $area;
    }

    /**
     * @return string|null
     */
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param string|null $building
     */
    public function setBuilding($building)
    {
        $this->building = $building;
    }

    /**
     * @return string|null
     */
    public function getCorpus()
    {
        return $this->corpus;
    }

    /**
     * @param string|null $corpus
     */
    public function setCorpus($corpus)
    {
        $this->corpus = $corpus;
    }

    /**
     * @return string|null
     */
    public function getHotel()
    {
        return $this->hotel;
    }

    /**
     * @param string|null $hotel
     */
    public function setHotel($hotel)
    {
        $this->hotel = $hotel;
    }

    /**
     * @return string|null
     */
    public function getHouse()
    {
        return $this->house;
    }

    /**
     * @param string|null $house
     */
    public function setHouse($house)
    {
        $this->house = $house;
    }

    /**
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @param string $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
    }

    /**
     * @return string|null
     */
    public function getLetter()
    {
        return $this->letter;
    }

    /**
     * @param string|null $letter
     */
    public function setLetter($letter)
    {
        $this->letter = $letter;
    }

    /**
     * @return string|null
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string|null $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return bool|null
     */
    public function getManualAddressInput()
    {
        return $this->manual_address_input;
    }

    /**
     * @param bool|null $manual_address_input
     */
    public function setManualAddressInput($manual_address_input)
    {
        $this->manual_address_input = $manual_address_input;
    }

    /**
     * @return string|null
     */
    public function getNumAddressType()
    {
        return $this->num_address_type;
    }

    /**
     * @param string|null $num_address_type
     */
    public function setNumAddressType($num_address_type)
    {
        $this->num_address_type = $num_address_type;
    }

    /**
     * @return string|null
     */
    public function getOffice()
    {
        return $this->office;
    }

    /**
     * @param string|null $office
     */
    public function setOffice($office)
    {
        $this->office = $office;
    }

    /**
     * @return string
     */
    public function getPlace()
    {
        return $this->place;
    }

    /**
     * @param string $place
     */
    public function setPlace($place)
    {
        $this->place = $place;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string|null
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string|null $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string|null
     */
    public function getSlash()
    {
        return $this->slash;
    }

    /**
     * @param string|null $slash
     */
    public function setSlash($slash)
    {
        $this->slash = $slash;
    }

    /**
     * @return string|null
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string|null $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string|null
     */
    public function getVladenie()
    {
        return $this->vladenie;
    }

    /**
     * @param string|null $vladenie
     */
    public function setVladenie($vladenie)
    {
        $this->vladenie = $vladenie;
    }
}