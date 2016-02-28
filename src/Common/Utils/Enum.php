<?php
namespace Flaubert\Common\Utils;

use ReflectionClass;

abstract class Enum
{
    protected static $constants;

    /**
     * Return all possible values of this enum
     *
     * @todo Apply constants cache properly
     * @return array
     */
    public static function all()
    {
        $selfClass = get_called_class();

        $reflection = new ReflectionClass($selfClass);
        $selfClass::$constants = $reflection->getConstants();

        return array_values($selfClass::$constants);
    }

    /**
     * Check if specified value is contained in this enumeration
     *
     * @param string|number $value
     *
     * @return boolean
     */
    public static function contains($value)
    {
        $selfClass = get_called_class();
        return in_array($value, $selfClass::all());
    }

    /**
     * Ensure the value belongs to this enum. Otherwise, raises an exception
     *
     * @param mixed $value Value to be checked
     *
     * @throws InvalidArgumentException If the value doesn't belong to this enum
     */
    public static function check($value)
    {
        if (!static::contains($value)) {
            throw new InvalidArgumentException('The input value doesn\'t belong to this enum');
        }
    }
}