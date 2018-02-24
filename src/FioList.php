<?php
namespace LapayGroup\RussianPost;

class FioList
{
    private $stack = []; // Список адресов для нормализации

    public function add($fio, $id = false)
    {
        if (empty($id)) {
            do {
                $id = count($this->stack);
            } while(isset($this->stack[$id]));
        } else {
            if (isset($this->stack[$id]))
                throw new \InvalidArgumentException('ID ФИО должен быть уникальным');
        }

        $info['id'] = $id;
        $info['original-fio'] = $fio;
        $this->stack[$id] = $info;
    }

    public function get()
    {
        if (empty($this->stack))
            throw new \InvalidArgumentException('Список ФИО пуст');

        return $this->stack;
    }
}