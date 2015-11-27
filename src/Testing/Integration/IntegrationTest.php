<?php
namespace Flaubert\Testing\Integration;

use PHPUnit_Framework_TestCase as TestCase;
use Flaubert\Infrastructure\Application\AppFacade as App;

abstract class IntegrationTest extends TestCase
{
    protected $container;

    protected $app;

    protected function debug($message, $newLine = true)
    {
        echo ($newLine) ? ($message . PHP_EOL) : $message;
    }

    public function setUp()
    {
        $this->container = App::container();
        $this->app = App::itSelf();
    }
}