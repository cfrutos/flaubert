<?php
namespace Flaubert\Persistence\Elastic\ODM\Internal\Hydration;

use Flaubert\Persistence\Elastic\ODM\ElasticManager;

abstract class AbstractHydrator
{
    /**
     * @var Flaubert\Persistence\Elastic\ODM\ElasticManager
     */
    protected $em;

    /**
     * Mapping manager
     *
     * @var Flaubert\Persistence\Elastic\Mapping\ElasticMappingDriver
     */
    protected $mappingManager;

    public function __construct(ElasticManager $em)
    {
        $this->em = $em;
        $this->mappingDriver = $this->em->getMappingDriver();
    }

    public abstract function hydrateAll(array $rawResult);

    public abstract function hydrateRow(array $rawResultRow);
}