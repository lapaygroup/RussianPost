<?php
namespace LapayGroup\RussianPost;

class PhoneList implements \IteratorAggregate
{
    private $stack = []; // Список телефонных номеров для нормализации
    private $idList = []; // Список id, которые уже есть в стэке

    public function add($phone, $id = false)
    {
        if ($id === false) {
            $id = count($this->stack);
        } else {
            if (isset($this->idList[$id]))
                throw new \InvalidArgumentException('ID номера телефона должен быть уникальным');
        }

        $info['id'] = $id;
        $info['original-phone'] = $phone;
        $this->stack[] = $info;
        $this->idList[$id] = true;
    }

    public function getIterator(): \Traversable
    {
        if (empty($this->stack))
            throw new \InvalidArgumentException('Список телефонных номеров пуст');

        return new \ArrayIterator($this->stack);
    }
}
