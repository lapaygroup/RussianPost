<?php
namespace LapayGroup\RussianPost;

class FioList
{
    private $stack = []; // Список адресов для нормализации
    private $idList = []; // Список id, которые уже есть в стэке

    public function add($fio, $id = false)
    {
        if (empty($id)) {
            do {
                $id = count($this->stack);
            } while(isset($this->idList[$id]));
        } else {
            if (isset($this->idList[$id]))
                throw new \InvalidArgumentException('ID ФИО должен быть уникальным');
        }

        $info['id'] = $id;
        $info['original-fio'] = $fio;
        $this->stack[] = $info;
        $this->idList[$id] = true;
    }

    public function get()
    {
        if (empty($this->stack))
            throw new \InvalidArgumentException('Список ФИО пуст');

        return $this->stack;
    }
}