<?php
namespace Flaubert\Persistence\Elastic\Mapping;

abstract class AbstractElasticMapping
{
    /**
     * Type to be mapped
     *
     * @var string
     */
    protected $type;

    /**
     * Mapped document type
     *
     * @var string
     */
    protected $documentType = null;

    /**
     * @var boolean
     */
    protected $isSuperClass = false;

    /**
     * @var string
     */
    protected $discriminatorProperty;

    /**
     * @var array
     */
    protected $discriminatorMap = [];

    /**
     * Model class
     *
     * @var string
     */
    protected $modelClass;

    /**
     * Parent class
     *
     * @var string
     */
    protected $parentClass = null;

    /**
     * @var array
     */
    protected $properties = [];

    /**
     * @var ElasticMappingDriver
     */
    protected $mappingDriver;

    public function __construct()
    {
        $this->map();
    }

    /**
     * Maps the type fields
     *
     * @return void
     */
    protected abstract function map();

    /**
     * @return self
     */
    protected function setType($type)
    {
        $this->type = (string) $type;

        return $this;
    }

    /**
     * @return string
     */
    public function documentType()
    {
        return $this->documentType;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->properties;
    }

    /**
     * @return self
     */
    protected function addProperty($name, $type)
    {
        $this->properties[$name] = [
            'type' => $type
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->properties;
    }

    /**
     * @return self
     */
    protected function addField($name, $type)
    {
        $this->properties[$name] = [
            'type' => $type
        ];

        return $this;
    }

    /**
     * @return self
     */
    protected function setModelClass($modelClass)
    {
        $this->modelClass = (string) $modelClass;

        return $this;
    }

    /**
     * @return self
     */
    protected function setDocumentType($documentType)
    {
        $this->documentType = (string) $documentType;

        return $this;
    }

    /**
     * Mark as single document inheritance
     *
     * @return self
     */
    public function markAsSingleDocumentInheritance()
    {
        $this->isSuperClass = true;

        return $this;
    }

    /**
     * @return self
     */
    public function setDiscriminatorProperty($propertyName)
    {
        $propertyName = (string) $propertyName;

        $this->discriminatorProperty = $propertyName;

        return $this;
    }

    /**
     * @return string
     */
    public function discriminatorProperty()
    {
        return $this->discriminatorProperty;
    }

    /**
     * @return boolean
     */
    public function isSuperClass()
    {
        return $this->isSuperClass;
    }

    /**
     * @return string
     */
    public function getClassFromDiscriminator($discriminator)
    {
        return $this->discriminatorMap[$discriminator];
    }

    /**
     * Map a discriminator to an entity class
     *
     * @param string $name Discriminator unique name
     * @param string $class Entity class
     *
     * @return self
     */
    public function addDiscriminatorMapClass($name, $class)
    {
        $name = (string) $name;
        $class = (string) $class;

        $this->discriminatorMap[$name] = $class;

        return $this;
    }

    /**
     * @return array
     */
    public function getSubClasses()
    {
        return array_values($this->discriminatorMap);
    }

    /**
     * Set the parent class name
     *
     * @return self
     */
    public function setParentClass($parentClass)
    {
        $parentClass = (string) $parentClass;
    }

    /**
     * @return string
     */
    public function parentClass()
    {
        return $this->parentClass;
    }

    /**
     * @return string
     */
    public function modelClass()
    {
        return $this->modelClass;
    }

    /**
     * @return boolean
     */
    public function hasChildrenClasses()
    {
        return (count($this->discriminatorMap) > 0);
    }

    /**
     * @return self
     */
    public function setMappingDriver(ElasticMappingDriver $mappingDriver)
    {
        $this->mappingDriver = $mappingDriver;

        return $this;
    }
}