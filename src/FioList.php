<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost;

class FioList implements \IteratorAggregate, \Countable
{
    private array $stack = []; // Список ФИО для нормализации
    private array $idList = []; // Список id, которые уже есть в стэке

    public function add(string $fio, int|string|false $id = false): void
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

    #[\Override]
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->stack);
    }

    #[\Override]
    public function count(): int
    {
        return count($this->stack);
    }
}
