<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

class SizeList extends BaseChoiceList
{
    public static function getChoiceClass(): string
    {
        return Size::class;
    }

}