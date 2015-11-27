<?php
namespace Flaubert\Persistence\Elastic\Mapping;

use ReflectionClass;
use Illuminate\Filesystem\ClassFinder;

class ElasticMappingDriverFromDirectories extends ElasticMappingDriver
{
    /**
     * @var Illuminate\Filesystem\ClassFinder
     */
    protected $classFinder;

    public function __construct(ClassFinder $classFinder, array $directories)
    {
        $this->classFinder = $classFinder;

        foreach ($directories as $directory) {
            $this->loadFromDirectory($directory);
        }
    }

    /**
     * Loads mappings from directory
     *
     * @return self
     */
    public function loadFromDirectory($path)
    {
        $path = (string) $path;

        $classes = $this->classFinder->findClasses($path);

        $isValidMappingClass = function($className) {
            $rc = new ReflectionClass($className);
            $parents = class_parents($className);

            return !$rc->isAbstract() && in_array(AbstractElasticMapping::class, $parents);
        };

        $validClasses = array_filter($classes, $isValidMappingClass);

        foreach ($validClasses as $mappingClass) {
            $this->addMapping(new $mappingClass());
        }
    }
}