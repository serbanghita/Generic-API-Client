<?php
namespace Client;

abstract class AbstractClient
{

    protected $options;
    protected $transport;

    protected $host;
    protected $port;
    protected $proxy;


    public function __construct($options, $transportType = 'Curl')
    {
        $this->setOptions($options);
        $this->setTransport($transportType);
    }

    public function setOptions($options)
    {
        // Basic checks.
        if (empty($options)) {
            throw new Exception('Invalid configuration.', 'INVALID_CONFIG');
        }

        // Check if the mandatory keys are set.
        if (isset($options['uri']) &&
            !empty($options['uri']) &&
            preg_match('/http[s]?\:\/\/[\w\.\-]+\//is', $options['uri'])) {

            $this->setHost(parse_url($options['uri'], PHP_URL_HOST));
            $this->setPort(parse_url($options['uri'], PHP_URL_PORT));

        } else {
            throw new Exception('Invalid endpoint URI specified in the configuration', 'INVALID_CONFIG');
        }

        if (isset($options['proxy'])) {
            $this->setProxy($options['proxy']);
        }

        $this->options = $options;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function setPort($port = 80)
    {
        $this->port = $port;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function setProxy($proxy)
    {
        $this->proxy = $proxy;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    public function setTransport($type)
    {
        $className = '\\Transport\\' . $type;
        if (!class_exists($className)) {
            throw new Exception('Transport type not supported: ' . $type, 'INVALID_TRANSPORT');
        }

        $options = $this->getOptions();

        switch ($type) {
            case 'Curl':
                // Translate the Client's options into valid cURL options.
                // @todo: Expose more options.
                $transportOptions = array();
                $transportOptions[CURLOPT_URL] = $options['uri'];
                $transportOptions[CURLOPT_PROXY] = $this->getProxy();
                break;
        }

        $this->transport = new $className($transportOptions);

    }

    public function getTransport()
    {
        return $this->transport;
    }

    abstract public function send($method, $args);

    abstract public function login();

    abstract public function logout();
}
