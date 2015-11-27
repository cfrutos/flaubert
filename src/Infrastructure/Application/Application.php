<?php
namespace Flaubert\Infrastructure\Application;

use ReflectionClass;
use Interop\Container\ContainerInterface;

abstract class Application
{
    const ENV_TESTING = 'testing';
    const ENV_DEVELOPMENT = 'development';
    const ENV_PRODUCTION = 'production';

    /**
     * Application instance
     *
     * @var Application
     */
    protected static $appInstance;

    /**
     * Gets an application instance
     *
     * @return Application
     */
    public static function instance()
    {
        if (!self::$appInstance) {
            self::$appInstance = new static();
        }

        return self::$appInstance;
    }

    /**
     * Constructor
     */
    protected function __construct()
    {
        $actualAppClass = new ReflectionClass(get_called_class());

        $this->path = $this->path ?: dirname($actualAppClass->getFilename());
    }

    /**
     * Application name
     *
     * @var string
     */
    protected $name;

    /**
     * Application base path
     *
     * @var string
     */
    protected $path;

    /**
     * IoC Container
     *
     * @var Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * @var PhpInterop\Config\IConfiguration
     */
    protected $config;

    /**
     * Current application environment
     *
     * @var string
     */
    protected $env;

    /**
     * Gets the application name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Gets the application base path
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Gets the application configuration
     *
     * @return PhpInterop\Config\IConfiguration
     */
    public function config()
    {
        return $this->config;
    }

    /**
     * Gets the application IoC container
     *
     * @return Interop\Container\ContainerInterface
     */
    public function container()
    {
        return $this->container;
    }

    /**
     * Gets the current application environment
     *
     * @return string
     */
    public function env()
    {
        return $this->env;
    }

    /**
     * Indicates if the application is running on a testing environment
     *
     * @return boolean
     */
    public function isTesting()
    {
        return ($this->env() === static::ENV_TESTING);
    }

    /**
     * Mark the application as on testing environment
     *
     * @return self
     */
    public function markAsTesting()
    {
        $this->env = static::ENV_TESTING;

        return $this;
    }

    /**
     * Makes a dependency
     *
     * @param string $dependency Dependency name
     *
     * @return mixed
     */
    public function make($dependency)
    {
        return $this->container()->get($dependency);
    }

    /**
     * @return self
     */
    public function itSelf()
    {
        return $this;
    }

    /**
     * Boot application
     *
     * @return self
     */
    public abstract function boot();
}