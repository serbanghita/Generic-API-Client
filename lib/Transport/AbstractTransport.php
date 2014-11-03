<?php
namespace Transport;

abstract class AbstractTransport
{
    protected $handler;
    protected $options = array();

    protected $request;
    protected $response;

    abstract public function setOptions($options);

    public function getOptions()
    {
        return $this->options;
    }

    abstract public function connect($host, $port = 80);

    abstract public function write($method, $uri, $headers, $body);

    abstract public function read();

    abstract public function close();
}
