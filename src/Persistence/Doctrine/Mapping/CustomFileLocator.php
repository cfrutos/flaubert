<?php
namespace Flaubert\Persistence\Doctrine\Mapping;

use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use Doctrine\Common\Persistence\Mapping\MappingException;

class CustomFileLocator extends DefaultFileLocator
{
    const MAPPING_SUFFIX = 'Mapping';

    /**
     * @var string[]
     */
    protected $entitiesNamespaces;

    public function __construct($locator, $entitiesNamespaces, $fileExtension)
    {
        parent::__construct($locator, $fileExtension);
        $this->entitiesNamespaces = (array) $entitiesNamespaces;
    }

    /**
     * {@inheritDoc}
     */
    public function findMappingFile($className)
    {
        $fileName = $this->getShortName($className) . static::MAPPING_SUFFIX . $this->fileExtension;

        // Check whether file exists
        foreach ($this->paths as $path) {
            if (is_file($path . DIRECTORY_SEPARATOR . $fileName)) {
                return $path . DIRECTORY_SEPARATOR . $fileName;
            }
        }
        throw MappingException::mappingFileNotFound($className, $fileName);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames($globalBasename)
    {
        $classes = array();

        if ($this->paths) {
            foreach ($this->paths as $path) {
                if ( ! is_dir($path)) {
                    throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
                }

                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($iterator as $file) {
                    $fileName = $file->getBasename($this->fileExtension);

                    if ($fileName == $file->getBasename() || $fileName == $globalBasename) {
                        continue;
                    }

                    foreach ($this->entitiesNamespaces as $entityNamespace) {
                        $candidateClass = $entityNamespace . '\\' . preg_replace('/Mapping$/', '', $fileName);

                        // NOTE: All files found here means classes are not transient!
                        if (class_exists($candidateClass)) {
                            $classes[] = $candidateClass;
                            break;
                        }
                    }
                }
            }
        }

        return $classes;
    }

    /**
     * {@inheritDoc}
     */
    public function fileExists($className)
    {
        $fileName = $this->getShortName($className) . static::MAPPING_SUFFIX . $this->fileExtension;

        // Check whether file exists
        foreach ((array) $this->paths as $path) {
            if (is_file($path . DIRECTORY_SEPARATOR . $fileName)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get short name of a class (Without namespace)
     *
     * @param string $class Fully qualified class name
     *
     * @return string Short class name
     */
    private function getShortName($class)
    {
        $path = explode('\\', $class);
        return array_pop($path);
    }
}