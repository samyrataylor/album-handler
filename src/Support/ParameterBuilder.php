<?php

namespace SamyraTaylor\AlbumHandler\Support;

use SamyraTaylor\AlbumHandler\Contracts\Choice;
use SamyraTaylor\AlbumHandler\Contracts\ChoiceList;

class ParameterBuilder
{
    public static function fromArray(string $name, array $array): ?string
    {
        $stack = [];
        foreach ($array as $value) {
            $string = static::from($name, $value);
            if ($string !== null) {
                $stack[] = $string;
            }
        }
        return implode(' ', $stack);
    }

    public static function from(string $name, mixed $value): ?string
    {
        if (empty($value) && $value !== 0) {
            return null;
        }

        return match (gettype($value)) {
            'string'  => static::fromString($name, $value),
            'integer' => static::fromInt($name, $value),
            'boolean' => static::fromBool($name, $value),
            'array'   => static::fromArray($name, $value),
            'object'  => static::fromObject($name, $value),
            default   => null,
        };
    }

    public static function fromString(string $name, ?string $value): ?string
    {
        return !empty($value) ? $name . ' "' . $value . '"' : null;
    }

    public static function fromInt(string $name, ?int $value): ?string
    {
        return $value ? $name . ' ' . $value : null;
    }

    public static function fromBool(string $name, ?bool $value): ?string
    {
        return $value ? $name : null;
    }

    public static function fromObject(string $name, object $object): ?string
    {
        if ($object instanceof Choice) {
            return static::fromChoice($name, $object);
        }

        if ($object instanceof ChoiceList) {
            return static::fromChoiceList($name, $object);
        }

        return null;
    }

    public static function fromChoice(string $name, Choice $choice): ?string
    {
        return static::fromString($name, $choice->value);
    }

    public static function fromChoiceList(string $name, ChoiceList $choiceList): ?string
    {
        return static::fromArray($name, $choiceList->toArray());
    }
}