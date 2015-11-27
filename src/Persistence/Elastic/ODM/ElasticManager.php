<?php
namespace Flaubert\Persistence\Elastic\ODM;

use InvalidArgumentException;
use ElasticSearch\Client as ElasticSearchClient;
use Flaubert\Persistence\Elastic\ODM\Internal\Hydration\ObjectHydrator;
use Flaubert\Persistence\Elastic\ODM\Internal\Hydration\HydrationMode;
use Flaubert\Persistence\Elastic\Mapping\ElasticMappingManager;
use Flaubert\Persistence\Elastic\Normalization\SpecificNormalizer;
use Flaubert\Persistence\Elastic\Normalization\DefaultNormalizer;
use Flaubert\Persistence\Elastic\Normalization\ElasticSerializer;
use Flaubert\Common\Objects\ClassFinder;

class ElasticManager
{
    /**
     * @var ElasticSearch\Client
     */
    protected $client;

    /**
     * Index name
     *
     * @var string
     */
    protected $index;

    /**
     * @var Flaubert\Persistence\Elastic\Mapping\AbstractElasticMapping
     */
    protected $mapping;

    /**
     * Mapping manager
     *
     * @var Flaubert\Persistence\Elastic\Mapping\ElasticMappingDriver
     */
    protected $mappingDriver;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var Flaubert\Persistence\Elastic\Normalization\ElasticSerializer;
     */
    protected $serializer;

    public function __construct(ElasticSearchClient $client, Configuration $configuration)
    {
        $this->client = $client;
        $this->configuration = $configuration;
        $this->index = $configuration->getIndex();
        $this->mappingDriver = $configuration->getMappingDriver();

        $this->createSerializer();
    }

    public function createSerializer()
    {
        $normalizers = [];

        if ($this->configuration->getSpecificNormalizersPath()) {
            $classFinder = new ClassFinder();

            $specificNormalizerClasses = $classFinder->findClasses(
                $this->configuration->getSpecificNormalizersPath(),
                [
                    'ignoreAbstract' => true,
                    'mustBeA' => SpecificNormalizer::class
                ]
            );

            foreach ($specificNormalizerClasses as $specificNormalizerClass) {
                $normalizers[] = new $specificNormalizerClass();
            }
        }

        $normalizers[] = new DefaultNormalizer();

        $this->serializer = new ElasticSerializer($normalizers);
    }

    /**
     * @return ElasticSerializer
     */
    public function getSerializer()
    {
        return $this->serializer;
    }

    public function indexDocument($type, array $document, array $options)
    {

    }

    public function getMappingDriver()
    {
        return $this->mappingDriver;
    }

    /**
     * Raw search
     *
     * @return array
     */
    public function rawSearch($type, array $body = [])
    {
        if (!$type) {
            throw new InvalidArgumentException('Type can\'t be empty');
        }

        $params['index'] = $this->index;
        $params['type']  = $type;
        $params['body'] = $body;

        $results = $this->client->search($params);

        $hits = $results['hits']['hits'];

        $hydrator = $this->getHydrator(HydrationMode::AS_OBJECT);

        $result = $hydrator->hydrateAll($hits);

        return $result;
    }

    /**
     * @return Flaubert\Persistence\Elastic\ODM\Internal\Hydration\ObjectHydrator
     */
    public function getHydrator($hydrationMode)
    {
        if ($hydrationMode === HydrationMode::AS_OBJECT) {
            return new ObjectHydrator($this);
        } else {
            throw new InvalidArgumentException('Invalid hydrator');
        }
    }
}