<?php
namespace Flaubert\Persistence\Elastic\ODM;

use Flaubert\Persistence\Elastic\Mapping\ElasticMappingDriver;

class Configuration
{
    /**
     * @var string
     */
    protected $index;

    /**
     * @var Flaubert\Persistence\Elastic\Mapping\ElasticMappingDriver
     */
    protected $mappingDriver;

    /**
     * @var string
     */
    protected $specificNormalizersPath;

    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return self
     */
    public function setIndex($index)
    {
        $this->index = $index;

        return $this;
    }

    public function setSpecificNormalizersPath($specificNormalizersPath)
    {
        $this->specificNormalizersPath = $specificNormalizersPath;

        return $this;
    }

    /**
     * @return string
     */
    public function getSpecificNormalizersPath()
    {
        return $this->specificNormalizersPath;
    }

    /**
     * @return ElasticMappingDriver
     */
    public function getMappingDriver()
    {
        return $this->mappingDriver;
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