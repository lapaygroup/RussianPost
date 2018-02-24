<?php
namespace LapayGroup\RussianPost;

class PhoneList
{
    private $stack = []; // Список телефонных номеров для нормализации

    public function add($phone, $id = false)
    {
        if (empty($id)) {
            do {
                $id = count($this->stack);
            } while(isset($this->stack[$id]));
        } else {
            if (isset($this->stack[$id]))
                throw new \InvalidArgumentException('ID номера телефона должен быть уникальным');
        }

        $info['id'] = $id;
        $info['original-phone'] = $phone;
        $this->stack[$id] = $info;
    }

    public function get()
    {
        if (empty($this->stack))
            throw new \InvalidArgumentException('Список телефонных номеров пуст');

        return $this->stack;
    }
}