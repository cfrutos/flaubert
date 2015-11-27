<?php
namespace Flaubert\Common\Objects;

use ReflectionClass;
use Illuminate\Filesystem\ClassFinder as LaravelClassFinder;

class ClassFinder
{
    /**
     * @var LaravelClassFinder
     */
    protected $wrappedClassFinder;

    public function __construct()
    {
        $this->wrappedClassFinder = new LaravelClassFinder();
    }

    /**
     * Find all the class and interface names in a given directory.
     *
     * @param  string  $directory
     * @param  array   $options
     *
     * @return array
     */
    public function findClasses($directory, array $options = [])
    {
        $options += [
            'ignoreAbstract' => false,
            'mustBeA' => null
        ];

        $classes = $this->wrappedClassFinder->findClasses($directory);

        $classes = array_filter($classes, function($class) use (&$options) {
            $rc = new ReflectionClass($class);

            $ignored = ($options['ignoreAbstract'] && $rc->isAbstract()) ||
                ($options['mustBeA'] && !is_subclass_of($class, $options['mustBeA']));

            return !$ignored;
        });

        return $classes;
    }
}