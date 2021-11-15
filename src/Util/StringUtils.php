<?php

namespace EDB\AdminBundle\Util;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\String\ByteString;

class StringUtils
{
    public static function createSlug(?string $value): string
    {
        if (empty($value)) return self::generateRandomString();

        $slugger = new AsciiSlugger();
        return $slugger
            ->slug($value, '-', 'bg')
            ->lower();
    }

    public static function generateRandomString(string $prefix = null): string
    {
        return ByteString::fromRandom(30);
    }
}
