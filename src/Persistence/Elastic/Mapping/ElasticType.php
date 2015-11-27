<?php
namespace Flaubert\Persistence\Elastic\Mapping;

use Flaubert\Common\Utils\Enum;

/**
 * Core Elasticsearch data types
 */
class ElasticType extends Enum
{
    const STRING = 'string';

    const INTEGER = 'integer';

    const DATE = 'date';

    const BOOLEAN = 'boolean';

    const BINARY = 'binary';
}