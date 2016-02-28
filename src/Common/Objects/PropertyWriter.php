<?php
namespace Flaubert\Common\Objects;

use ReflectionClass;
use InvalidArgumentException;

class PropertyWriter
{
    /**
     * Writes a protected or private property of an object
     *
     * @param object $subject Subject object
     * @param string $propertyName Property name
     * @param mixed $value Value to write
     *
     * @throws InvalidArgumentException If the property doesn't exist
     *
     * @return void
     */
    public static function write($subject, $propertyName, $value)
    {
        if (
            !is_object($subject) ||
            !property_exists($subject, $propertyName)
        ) {
            throw new InvalidArgumentException("Property {$propertyName} not found");
        }

        $reflection = new ReflectionClass($subject);
        $property = $reflection->getProperty($propertyName);

        $property->setAccessible(true);

        $property->setValue($subject, $value);
    }
}