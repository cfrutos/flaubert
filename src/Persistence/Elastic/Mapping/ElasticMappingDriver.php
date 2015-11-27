<?php
namespace Flaubert\Persistence\Elastic\Mapping;

use InvalidArgumentException;

class ElasticMappingDriver
{
    /**
     * Mapped types
     *
     * @var array
     */
    protected $mappings = [];

    /**
     * @param array $mappings
     */
    public function __construct(array $mappings = [])
    {
        $this->mappings = $mappings;
    }

    /**
     * @return self
     */
    public function addMapping(AbstractElasticMapping $mapping)
    {
        $this->mappings[] = $mapping;

        return $this;
    }

    /**
     * @return array
     */
    public function getAllMappings()
    {
        return $this->mappings;
    }

    /**
     * Returns the raw ElasticSearch mapping from a specific document type
     *
     * @param string $documentType DocumentType
     *
     * @return array
     */
    public function rawElasticSearchMapping($documentType)
    {
        $mapping = $this->getMappingFromType($documentType);

        //Its properties and children properties
        return $this->getAllProperties($mapping, true);
    }

    /**
     * All raw ES mappings
     *
     * @return array
     */
    public function allElasticSearchMappings()
    {
        $rawMappings = [];

        foreach ($this->allTypes() as $documentType) {
            $rawMappings[$documentType] = $this->rawElasticSearchMapping($documentType);
        }

        return $rawMappings;
    }

    public function allTypes()
    {
        return array_unique(array_filter(array_map(function(AbstractElasticMapping $mapping) {
            return $mapping->documentType();
        }, $this->mappings)));
    }

    /**
     * @return array
     */
    protected function getAllProperties(AbstractElasticMapping $mapping, $includeChildren = true)
    {
        $properties = $mapping->getProperties();

        if ($includeChildren) {
            $subMappings = array_map([$this, 'getMappingFromModelClass'], $mapping->getSubClasses());

            foreach ($subMappings as $subMapping) {
                $subProperties = $this->getAllProperties($subMapping, true);

                $properties = array_merge($properties, $subProperties);
            }
        }

        return $properties;
    }

    public function getAllMappedFields($modelClass)
    {
        $mapping = $this->getMappingFromModelClass($modelClass);

        $fields = $mapping->getFields();

        $superMappings = $this->getSuperMappings($modelClass);

        foreach ($superMappings as $superMapping) {
            $fields = array_merge($superMapping->getFields(), $fields);
        }

        return $fields;
    }

    /**
     * @return array<AbstractElasticMapping>
     */
    protected function getSuperMappings($modelClass)
    {
        $parentMappings = array_filter($this->mappings, function(AbstractElasticMapping $mapping) use ($modelClass) {
            return in_array($modelClass, $mapping->getSubClasses());
        });

        if (empty($parentMappings)) {
            return [];
        }

        if (count($parentMappings) > 1) {
            throw new InvalidArgumentException('A mapped model class can\'t have more than one parent');
        }

        $parentMapping = array_shift($parentMappings);

        $superMappings = (!empty($parentMapping->modelClass())) ?
            [$parentMapping] + $this->getSuperMappings($parentMapping->modelClass()) :
            [$parentMapping];

        return $superMappings;
    }


    /**
     * @return AbstractElasticMapping
     */
    public function getMappingFromType($documentType)
    {
        foreach ($this->mappings as $mapping) {
            if ($mapping->documentType() === $documentType) {
                return $mapping;
            }
        }
    }

    protected function getMappingFromModelClass($modelClass)
    {
        $modelClass = (string) $modelClass;

        foreach ($this->mappings as $mapping) {
            if ($mapping->modelClass() === $modelClass) {
                return $mapping;
            }
        }
    }
}