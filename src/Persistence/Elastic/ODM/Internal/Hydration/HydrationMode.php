<?php
namespace Flaubert\Persistence\Elastic\ODM\Internal\Hydration;

use Flaubert\Common\Utils\Enum;

class HydrationMode extends Enum
{
    /**
     * Hydrate as entity object
     */
    const AS_OBJECT = 1;

    /**
     * Hydrate as nested array
     */
    const AS_ARRAY = 2;
}