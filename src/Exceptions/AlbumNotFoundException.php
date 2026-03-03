<?php

namespace SamyraTaylor\AlbumHandler\Exceptions;

class AlbumNotFoundException extends ActionException
{
    public function __construct(string $albumName)
    {
        parent::__construct(sprintf('Album "%s" not found.', $albumName));
    }
}