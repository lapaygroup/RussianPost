<?php
namespace LapayGroup\RussianPost;

class PhoneList
{
    private $stack = []; // Список телефонных номеров для нормализации
    private $idList = []; // Список id, которые уже есть в стэке

    public function add($phone, $id = false)
    {
        if (empty($id)) {
            do {
                $id = count($this->stack);
            } while(isset($this->idList[$id]));
        } else {
            if (isset($this->idList[$id]))
                throw new \InvalidArgumentException('ID номера телефона должен быть уникальным');
        }

        $info['id'] = $id;
        $info['original-phone'] = $phone;
        $this->stack[] = $info;
        $this->idList[$id] = true;
    }

    public function get()
    {
        if (empty($this->stack))
            throw new \InvalidArgumentException('Список телефонных номеров пуст');

        return $this->stack;
    }
}