<?php

namespace SamyraTaylor\AlbumHandler\Contracts;

use Countable;
use IteratorAggregate;

interface ChoiceList extends IteratorAggregate, Countable
{
    public static function getChoiceClass(): string;

    public function toArray(): array;

    public function first(): ?Choice;

    public function last(): ?Choice;

    public function all(): array;

    public function add(Choice $choice): static;

    public function set(array $choices): static;

    public function remove(Choice $choice): static;

    public function has(Choice $choice): bool;

    public function append(Choice $choice): static;

    public function prepend(Choice $choice): static;
}