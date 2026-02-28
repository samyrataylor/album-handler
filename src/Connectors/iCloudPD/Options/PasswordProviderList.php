<?php

namespace SamyraTaylor\AlbumHandler\Connectors\iCloudPD\Options;

class PasswordProviderList extends BaseChoiceList
{
    public static function getChoiceClass(): string
    {
        return PasswordProvider::class;
    }

}