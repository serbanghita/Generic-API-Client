<?php
namespace Transport;

interface TransportInterface
{
    protected $proxy;
    protected $host;
    protected $port;
    protected $request;
    protected $response;

    public function __construct($options);
    public function setOptions($options);
    public function getOptions();
    public function connect($host, $port = 80, $secure = false);
    public function write($method, $uri, $headers, $body);
    public function read();
    public function close();
}
