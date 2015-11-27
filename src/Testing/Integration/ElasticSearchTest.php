<?php
namespace Flaubert\Testing\Integration;

use ElasticSandbox\ElasticSandbox;
use Flaubert\Infrastructure\Application\AppFacade as App;

/**
 * ElasticSearch aware test
 */
abstract class ElasticSearchTest extends IntegrationTest
{
    /**
     * @var ElasticSandbox\ElasticSandbox
     */
    protected $elasticSandbox;

    public function setUp()
    {
        parent::setUp();

        if (!$this->elasticSandbox) {
            $this->elasticSandbox = App::make(ElasticSandbox::class);
            //@todo Display messages!
        }

        $this->elasticSandbox->reboot();
    }
}