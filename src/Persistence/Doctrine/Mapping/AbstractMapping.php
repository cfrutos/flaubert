<?php
namespace Flaubert\Persistence\Doctrine\Mapping;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;

abstract class AbstractMapping extends ClassMetadataBuilder
{
    /**
     * @var ClassMetadata
     */
    protected $metadata;

    /**
     * @var ClassMetadataBuilder
     */
    protected $builder;

    /**
     * @var string
     */
    public static $repositoryClass = null;

    public function __construct(ClassMetadata $metadata)
    {
        parent::__construct($metadata);
        $this->metadata = $metadata;

        $this->builder = $this;

        if (static::$repositoryClass) {
            $this->setCustomRepositoryClass(static::$repositoryClass);
        }

        $this->map();
    }

    /**
     * Executes the actual mapping in the subclass
     *
     * @see Template Method Pattern (http://www.oodesign.com/template-method-pattern.html)
     */
    protected abstract function map();
}