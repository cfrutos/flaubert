<?php
namespace Flaubert\Persistence\Elastic\ODM\Internal\Hydration;

use InvalidArgumentException;

class ObjectHydrator extends AbstractHydrator
{
    /**
     * {@inheritdoc}
     */
    public function hydrateAll(array $rawResult)
    {
        $objects = [];

        foreach ($rawResult as $rawResultRow) {
            $objects[] = $this->hydrateRow($rawResultRow);
        }

        return $objects;
    }

    /**
     * {@inheritdoc}
     */
    public function hydrateRow(array $rawResultRow)
    {
        $class = $this->decideClass($rawResultRow);

        $dataArray = $rawResultRow['_source'] + [
            'id' => $rawResultRow['_id']
        ];

        $object = $this->em->getSerializer()->denormalize($dataArray, $class);

        return $object;
    }

    /**
     * @return string
     */
    protected function decideClass(array $rawResultRow)
    {
        $documentType = $rawResultRow['_type'];

        $mapping = $this->mappingDriver->getMappingFromType($documentType);

        if ($mapping->isSuperClass()) {
            $discriminatorProperty = $mapping->discriminatorProperty();

            $discriminator = $rawResultRow['_source'][$discriminatorProperty];

            return $mapping->getClassFromDiscriminator($discriminator);
        } else if ($mapping->modelClass()) {
            return $mapping->modelClass();
        } else {
            throw new InvalidArgumentException('Mapped model class not found');
        }
    }
}