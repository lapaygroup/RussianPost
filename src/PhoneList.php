<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost;

class PhoneList implements \IteratorAggregate, \Countable
{
    private array $stack = []; // Список телефонных номеров для нормализации
    private array $idList = []; // Список id, которые уже есть в стэке

    public function add(string $phone, int|string|false $id = false): void
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
