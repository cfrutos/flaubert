<?php
namespace Flaubert\Persistence\Doctrine\Mapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\Mapping\Driver\FileLocator;
use App\Common\Utils\NamingHelper;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;

/**
 * Custom Mapping driver for Doctrine
 *
 * This mapping driver uses CustomFileLocator as a file locator in order to implement getAllClassNames() method
 * For individual metadata loading, it uses namespace based finding.
 *
 * @see http://doctrine-orm.readthedocs.org/en/latest/reference/metadata-drivers.html
 * @author Carlos Frutos <carlos@kiwing.it>
 */
class FluentPHPMappingDriver implements MappingDriver
{
    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @var string
     */
    protected $mappingNamespace;

    /**
     * @var FileLocator
     */
    protected $locator;

    /**
     * @var array|null
     */
    protected $classCache;

    /**
     * @var string|null
     */
    protected $globalBasename;

    /**
     * {@inheritDoc}
     */
    public function __construct(
        $locator,
        $entitiesNamespace,
        $mappingNamespace,
        $fileExtension = null
    ) {
        $fileExtension = ".php";

        if ($locator instanceof FileLocator) {
            $this->locator = $locator;
        } else {
            $this->locator = new CustomFileLocator((array)$locator, $entitiesNamespace, $fileExtension);
        }

        $this->mappingNamespace = $mappingNamespace;
    }

    /**
     * {@inheritDoc}
     */
    public function loadMetadataForClass($className, ClassMetadata $metadata)
    {
        $this->metadata = $metadata;
        $this->doMapping($className);
    }

    protected function doMapping($className)
    {
        $metadata = $this->metadata;
        $mappingClass = $this->mappingClassForEntity($className);
        $mapping = new $mappingClass($metadata);
        return [$metadata->getName() => $metadata];
    }

    /**
     * Get all mapping class names
     *
     * @return array<string>
     */
    public function allMappingClasses()
    {
        $classes = [];

        foreach ($this->getAllClassNames() as $className) {
            $classes[$className] = $this->mappingClassForEntity($className);
        }

        return $classes;
    }

    /**
     * Given an entity class, returns the corresponding mapping class
     *
     * @param string $entityClass Entity class
     *
     * @return string
     */
    public function mappingClassForEntity($entityClass)
    {
        $entityName = NamingHelper::shortClassName($entityClass);

        $mappingClass = $this->mappingNamespace . '\\' . $entityName . CustomFileLocator::MAPPING_SUFFIX;

        return $mappingClass;
    }

    /**
     * {@inheritDoc}
     */
    public function isTransient($className)
    {
        if ($this->classCache === null) {
            $this->initialize();
        }

        if (isset($this->classCache[$className])) {
            return false;
        }

        return !$this->locator->fileExists($className);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames()
    {
        if ($this->classCache === null) {
            $this->initialize();
        }

        $classNames = (array) $this->locator->getAllClassNames($this->globalBasename);
        if ($this->classCache) {
            $classNames = array_merge(array_keys($this->classCache), $classNames);
        }
        return $classNames;
    }


    /**
     * Initializes the class cache from all the global files.
     *
     * Using this feature adds a substantial performance hit to file drivers as
     * more metadata has to be loaded into memory than might actually be
     * necessary. This may not be relevant to scenarios where caching of
     * metadata is in place, however hits very hard in scenarios where no
     * caching is used.
     *
     * @return void
     */
    protected function initialize()
    {
        return;
        $this->classCache = array();

        if (!is_null($this->globalBasename)) {
            return;
        }

        $allClasses = $this->locator->getAllClassNames($this->globalBasename);

        foreach ($allClasses as $entityClass) {
            $mappingClass = $this->mappingClassForEntity($entityClass);

            if (class_exists($mappingClass)) {
                $this->classCache = array_merge(
                    $this->classCache,
                    $this->doMapping($entityClass)
                );
            }
        }
    }
}