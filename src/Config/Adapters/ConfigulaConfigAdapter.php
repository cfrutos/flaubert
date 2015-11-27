<?php
namespace Flaubert\Config\Adapters;

use PhpInterop\Config\IConfiguration;

class ConfigulaConfigAdapter implements IConfiguration
{
    /**
     * @var Configula\Config
     */
    protected $wrappedConfig;

    public function __construct($configPath)
    {
        $this->wrappedConfig = new \Configula\Config($configPath);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key, $value = null)
    {
        return $this->wrappedConfig->getItem($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->wrappedConfig[$key] = $value;

        return $this;
    }
}