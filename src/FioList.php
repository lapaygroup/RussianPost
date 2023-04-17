<?php
namespace LapayGroup\RussianPost;

class FioList implements \IteratorAggregate
{
    private $stack = []; // Список адресов для нормализации
    private $idList = []; // Список id, которые уже есть в стэке

    public function add($fio, $id = false)
    {
        if ($id === false) {
            $id = count($this->stack);
        } else {
            if (isset($this->idList[$id]))
                throw new \InvalidArgumentException('ID ФИО должен быть уникальным');
        }

        $info['id'] = $id;
        $info['original-fio'] = $fio;
        $this->stack[] = $info;
        $this->idList[$id] = true;
    }

    public function getIterator()
    {
        if (empty($this->stack))
            throw new \InvalidArgumentException('Список ФИО пуст');

        return new \ArrayIterator($this->stack);
    }
}
