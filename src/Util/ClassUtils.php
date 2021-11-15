<?php

declare(strict_types=1);

namespace EDB\AdminBundle\Util;

use ReflectionClass;
use ReflectionException;
use Symfony\Component\String\Inflector\EnglishInflector;

class ClassUtils
{
    /**
     * @param mixed $object
     * @throws ReflectionException
     */
    public static function getShortName($object, bool $toLower = true): string
    {
        $reflect = new ReflectionClass($object);
        $shortName = $reflect->getShortName();
        if ($toLower) return strtolower($shortName);
        return $shortName;
    }

    public static function pluralize(string $className): string
    {
        $inflector = new EnglishInflector();
        return (string)$inflector->pluralize($className)[0];
    }
}
