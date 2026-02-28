<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

use SamyraTaylor\AlbumHandler\Contracts\Choice;
use SamyraTaylor\AlbumHandler\Contracts\ChoiceList;
use Traversable;

abstract class BaseChoiceList implements ChoiceList
{
    protected(set) array $choices = [];

    public function getIterator(): Traversable
    {
        yield from $this->choices;
    }

    public function count(): int
    {
        return count($this->choices);
    }

    public function toArray(): array
    {
        return array_map(fn(Choice $choice) => $choice->value, $this->choices);
    }

    public function first(): ?Choice
    {
        return array_first($this->choices);
    }

    public function last(): ?Choice
    {
        return array_last($this->choices);
    }

    public function prepend(Choice $choice): static
    {
        if ($this->isValidChoice($choice)) {
            array_unshift($this->choices, $choice);
        }

        return $this;
    }

    protected function isValidChoice(Choice $choice): bool
    {
        $class = static::getChoiceClass();
        return $choice instanceof $class && !in_array($choice, $this->choices);
    }

    abstract public static function getChoiceClass(): string;

    public function set(array $choices): static
    {
        $this->choices = [];

        foreach ($choices as $choice) {
            $this->add($choice);
        }

        return $this;
    }

    public function add(Choice|ChoiceList $choice): static
    {
        if ($choice instanceof ChoiceList) {
            foreach ($choice->all() as $choice) {
                $this->append($choice);
            }
            return $this;
        }

        return $this->append($choice);
    }

    public function all(): array
    {
        return $this->choices;
    }

    public function append(Choice $choice): static
    {
        if ($this->isValidChoice($choice)) {
            $this->choices[] = $choice;
        }

        return $this;
    }

    public function remove(Choice $choice): static
    {
        $this->choices = array_values(array_filter($this->choices, fn(Choice $c) => $c !== $choice));
        return $this;
    }

    public function has(Choice $choice): bool
    {
        return in_array($choice, $this->choices);
    }
}