<?php

namespace LapayGroup\RussianPost\Entity;

class Recipient
{
    const RELIABLE = 'RELIABLE';
    const FRAUD = 'FRAUD';

    /** @var string */
    private $address = null;

    /** @var string */
    private $name = null;

    /** @var string */
    private $phone = null;

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Возвращает массив параметров для запроса к API ПРФ
     *
     * @return array
     */
    public function getParams()
    {
        $params = [];
        $params['raw-address'] = $this->address;
        $params['raw-full-name'] = $this->name;
        $params['raw-telephone'] = $this->phone;

        return $params;
    }
}