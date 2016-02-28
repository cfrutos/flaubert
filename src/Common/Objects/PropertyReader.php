<?php
namespace Flaubert\Common\Objects;

use InvalidArgumentException;
use stdClass;

/**
 * Static helper for property reading on objects
 */
class PropertyReader
{
	/**
	 * Read a property by its name
	 * Order of precedence:
	 * 1) Getter
	 * 2) Class fields
	 * 3) Default value
	 *
	 * @param object $subject
	 * @param string $propertyName In camelCase
	 * @param mixed $default Default value
	 *
	 * @return mixed
	 */
	public static function read($subject, $propertyName, $default = null)
	{
		assert(is_object($subject), 'Subject must be an object');
		assert(is_scalar($propertyName), 'The property name must be a valid key');

		$getter = "get" . ucwords($propertyName);
		if (method_exists($subject, $getter)) {
			return $subject->$getter();
		}

		if (property_exists($subject, $propertyName)) {
			return $subject->$propertyName;
		}

		return $default;
	}

	/**
	 * Read a property of an object. If property is not found, then throws an exception.
	 *
	 * @param object $subject
	 * @param string $propertyName In camelCase
	 *
	 * @throws InvalidArgumentException If property not found
	 *
	 * @return mixed
	 */
	public static function readOrFail($subject, $propertyName)
	{
		$nullObject = new stdClass();

		$value = static::read($subject, $propertyName, $nullObject);

		if ($value === $nullObject) {
			throw new InvalidArgumentException('Property couldn\'t be found');
		}

		return $value;
	}
}