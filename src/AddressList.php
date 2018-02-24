<?php
namespace LapayGroup\RussianPost;

class AddressList
{
    private $stack = []; // Список адресов для нормализации

    public function add($address, $id = false)
    {
        if (empty($id)) {
            do {
                $id = count($this->stack);
            } while(isset($this->stack[$id]));
        } else {
            if (isset($this->stack[$id]))
                throw new \InvalidArgumentException('ID адреса должен быть уникальным');
        }

        $info['id'] = $id;
        $info['original-address'] = $address;
        $this->stack[$id] = $info;
    }

    public function get()
    {
        if (empty($this->stack))
            throw new \InvalidArgumentException('Список адресов пуст');

        return $this->stack;
    }
}