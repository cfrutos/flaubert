<?php
namespace Flaubert\Common\Utils;

/**
 * Helper for naming related functions
 */
class NamingHelper
{
    /**
     * Get short name of a class (Without namespace)
     *
     * @param string $class Fully qualified class name
     *
     * @return string Short class name
     */
    public static function shortClassName($class) {
	    $path = explode('\\', $class);
	    return array_pop($path);
	}

    /**
     * Camelizes a subject
     *
     * @param string $subject
     *
     * @return string
     */
    public static function camelize($subject)
    {
        return preg_replace('/(^|_)([a-z])/e', 'strtoupper("\\2")', $subject);
    }
}