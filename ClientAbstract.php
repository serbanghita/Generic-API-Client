<?php
namespace Client;

abstract class ClientAbstract
{

    protected $config = array();
    protected $httpClient;
    protected $httpProxy= null;
    protected $errors = array();

    protected $request;
    protected $response;

    public function __construct(\stdClass $config = null, \stdClass $httpClient = null)
    {
        if (isset($config)) {
            $this->setConfig($config);
        }

        if (isset($httpClient)) {
            $this->setHttpClient($httpClient);
        }

    }

    public function setConfig($config)
    {
        // Basic checks.
        if (empty($config) || !($config instanceof \stdClass)) {
            throw Exception('Invalid configuration.', 'INVALID_CONFIG');
        }

        // Check if the mandatory keys are set.
        if (!isset($config->endpointUrl) ||
            empty($config->endpointUrl) ||
            !preg_match('/http[s]?://[\w\.\-]+\//is', $config->endpointUrl)) {
            throw Exception('Invalid endpoint URL specified in the configuration', 'INVALID_CONFIG');
        }

        $this->config = $config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function setHttpClient($httpClient = null)
    {
        $this->httpClient = $httpClient;
    }

    public function setHttpProxy($proxy)
    {
        $this->httpProxy = $proxy;
    }

    public function setError($error)
    {
        $this->errors[] = $error;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getLastError()
    {
        if (isset($this->errors) && count($this->errors) > 0) {
            return end($this->errors);
        } else {
            return false;
        }
    }

    abstract public function setRequest($method, $args);

    public function getRequest()
    {
        return $this->request;
    }

    abstract public function sendRequest();

    public function setResponse($response)
    {
        $this->response = $response;
    }

    public function getResponse()
    {
        return $this->response;
    }

    abstract public function login();

    abstract public function logout();
}
