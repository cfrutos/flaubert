<?php
namespace Flaubert\Common\Net;

use InvalidArgumentException;

/**
 * Uri component
 *
 * @author Carlos Frutos <carlos@kiwing.it>
 */
class Uri
{
    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var int
     */
    protected $port;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $query = [];

    /**
     * Creates a new instance
     *
     * @param string $uri Uri
     */
    public function __construct($uri = null)
    {
        if (!empty($uri)) {
            $urlParts = parse_url($uri);

            $this->scheme   = (string) $urlParts['scheme'];
            $this->host     = (string) $urlParts['host'];
            $this->port     = !empty($urlParts['port']) ? ((int) $urlParts['port']) : '';
            $this->path     = (string) $urlParts['path'];

            if (!empty($urlParts['query'])) {
                $queryParts = [];
                parse_str($urlParts['query'], $queryParts);
                $this->query = $queryParts;
            }
        }
    }

    /**
     * Get schema
     *
     * @return string
     */
    public function scheme()
    {
        return $this->scheme;
    }

    /**
     * Get host
     *
     * @return string
     */
    public function host()
    {
        return $this->host;
    }

    /**
     * Get port
     *
     * @return int
     */
    public function port()
    {
        return $this->port;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Get query
     *
     * @return array
     */
    public function query()
    {
        return $this->query;
    }

    /**
     * Set part
     *
     * @param string $partName Part name
     * @param mixed $value Value
     *
     * @return self
     */
    public function setPart($partName, $value)
    {
        static $parts = ['scheme', 'host', 'port', 'path', 'query'];
        static $allowedSchemes = ['http', 'https'];

        assert(in_array($partName, $parts), 'Part name is invalid');

        switch ($partName) {
            case 'scheme':
                assert(in_array($value, $allowedSchemes), 'Invalid scheme: ' . (string) $value);
                $this->scheme = (string) $value;
                break;
            
            case 'host':
                assert(!empty($value), 'Host name can\'t be empty');
                $this->host = (string) $value;
                break;

            case 'port':
                $port = (int) $value;
                assert(empty($port) || ($port >= 1 && $port <= 65535), 'Invalid port');

                $this->port = $port ?: null;
                break;

            case 'path':
                $this->path = (string) $value;
                break;

            case 'query':
                assert(is_null($value) || is_array($value), 'Query must be an array');
                $this->query = (array) $value;
                break;
        }

        return $this;
    }

    /**
     * Add query parameter
     *
     * @param string $name Param name
     * @param string $value Param value
     *
     * @return self
     */
    public function addQueryParam($name, $value)
    {
        $this->query[(string) $name] = (string) $value;

        return $this;
    }

    /**
     * String representation
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->host) {
            throw new InvalidArgumentException('Host is required by string conversion');
        }

        $schemePart = $this->scheme ?: 'http';
        $portPart = $this->port ? (':' . $this->port) : '';
        $pathPart = $this->path ?: '/';

        if (!empty($this->query)) {
            $queryComponents = [];
            foreach ($this->query as $key => $value) {
                $queryComponents[] = $key . '=' . $value;
            }
            $queryPart = '?' . implode('&', $queryComponents);
        } else {
            $queryPart = '';
        }

        return "{$schemePart}://{$this->host}{$portPart}{$pathPart}{$queryPart}";
    }

    /**
     * String representation
     *
     * @return string
     */
    public function toString()
    {
        return $this->__toString();
    }
}
