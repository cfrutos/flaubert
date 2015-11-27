<?php
namespace Flaubert\Objects;

use Flaubert\Infrastructure\Application\Application;

/**
 * Static facade
 */
class Facade
{
	protected static $identifier;

	public static function __callStatic($method, $args)
	{
		$object = Application::instance()->make(static::$identifier);

		if ($object && method_exists($object, $method)) {
			return call_user_func_array([$object, $method], $args);
		}
	}
}