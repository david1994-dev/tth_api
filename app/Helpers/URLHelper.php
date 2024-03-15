<?php

namespace App\Helpers;

class URLHelper
{
    const ASSET_DOMAIN = 'http://192.165.101.36:889/public/storage/';
    public static function getFullPathURL($urls)
    {
        if (!is_array($urls)) return self::ASSET_DOMAIN . $urls;

        foreach ($urls as &$url) {
            $url = self::ASSET_DOMAIN . $url;
        }

        return $urls;
    }
}
