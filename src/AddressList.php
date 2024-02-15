<?php
namespace LapayGroup\RussianPost;

class AddressList implements \IteratorAggregate
{
    private $stack = []; // Список адресов для нормализации
    private $idList = []; // Список id, которые уже есть в стэке

    public function add($address, $id = false)
    {
        if ($id === false) {
            $id = count($this->stack);
        } else {
            if (isset($this->idList[$id]))
                throw new \InvalidArgumentException('ID адреса должен быть уникальным');
        }

        $info['id'] = $id;
        $info['original-address'] = $address;
        $this->stack[] = $info;
        $this->idList[$id] = true;
    }

    public function getIterator(): \Traversable
    {
        if (empty($this->stack))
            throw new \InvalidArgumentException('Список адресов пуст');

        return new \ArrayIterator($this->stack);
    }
}
