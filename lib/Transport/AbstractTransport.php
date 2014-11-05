<?php
namespace Transport;

abstract class AbstractTransport
{
    protected $handler;
    protected $options = array();

    protected $request;
    protected $response;

    abstract public function setOptions($options);

    public static function factory($transportClassName, $options)
    {
        if (!class_exists($className)) {
            throw new Exception('Transport class not supported: ' . $transportClassName, 'INVALID_TRANSPORT');
        }

        switch ($transportClassName) {
            case 'Curl':
                // Translate the Client's options into valid cURL options.
                // @todo: Expose more options.
                $transportOptions = array();
                $transportOptions[CURLOPT_URL] = $options['uri'];
                $transportOptions[CURLOPT_PROXY] = $this->getProxy();
                break;
        }

        return new $className($transportOptions);
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->rawResponse;
    }

    abstract public function connect($host, $port = 80);

    abstract public function write($method, $uri, $headers, $body);

    abstract public function read();

    abstract public function close();
}
