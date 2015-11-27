<?php
namespace Flaubert\Persistence\Elastic\ODM;

abstract class Store
{
    /**
     * @var ElasticManager
     */
    protected $em;

    public function __construct(ElasticManager $em)
    {
        $this->em = $em;
    }

    /**
     * Type
     *
     * @var string
     */
    protected $type;

    public function find($id)
    {

    }

    /**
     * Raw search
     *
     * @return array
     */
    public function rawSearch(array $body = [])
    {
        return $this->em->rawSearch($this->type, $body);
    }

    public function index(array $document)
    {

    }
}