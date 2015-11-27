<?php
namespace Flaubert\Persistence\Elastic\ODM;

class ElasticQuery
{
    const LIMIT_DEFAULT = 50;

    /**
     * @var ElasticManager
     */
    protected $em;

    /**
     * @var string $type
     */
    protected $type;

    /**
     * Limit
     *
     * @var int
     */
    protected $size = self::LIMIT_DEFAULT;

    /**
     * @var array
     */
    protected $rawBody = [
        'query' => [
            'filtered' => [
                'filter' => [
                    'and' => [

                    ]
                ]
            ]
        ]
    ];

    /**
     * Creates a new empty query
     */
    public function __construct(ElasticManager $em)
    {
        $this->em = $em;
    }


    /**
     * From type
     *
     * @param string $type
     *
     * @return self
     */
    public function from($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return self
     */
    public function andTerm($field, $value)
    {
        $field = (string) $field;

        $condition = ['term' => [$field => $value]];

        $this->rawBody['query']['filtered']['filter']['and'][] = $condition;

        return $this;
    }

    /**
     *
     */
    public function execute(array $params = [])
    {
        $result = $this->em->rawSearch($this->type, $this->rawBody);

        return $result;
    }

    /**
     * @return array
     */
    public function rawDSLQuery()
    {
        return $this->rawBody;
    }
}