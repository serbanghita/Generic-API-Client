<?php
namespace Client;

class ClientJsonRPC extends \Client\ClientAbstract
{

    public function __construct(\stdClass $config)
    {
        parent::__construct($config);
        $this->setHttpClient();
    }

    public function setHttpClient($httpClient = null)
    {
        if (!extension_loaded('curl')) {
            throw new Exception('cURL extension has to be loaded.', 'API_CLIENT_CURL_MISSING');
        }

        $httpClient = curl_init();
        curl_setopt($httpClient, CURLOPT_URL, $this->getConfig()->endpointUrl);
        curl_setopt($httpClient, CURLOPT_POST, 1);
        curl_setopt($httpClient, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($httpClient, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($httpClient, CURLOPT_HTTPHEADER, array(
            //'Content-Type: application/json',
            //'Accept: application/json'
            'Content-Type: text/plain',
            'Accept: text/plain'
        ));
        //curl_setopt($httpClient, CURLOPT_PROXY, '');

        // Don't check remote certificate.
        // curl_setopt($httpClient, CURLOPT_SSL_VERIFYPEER, false);
        // So we can catch explicit curl errors.
        curl_setopt($httpClient, CURLOPT_VERBOSE, true);

        parent::setHttpClient($httpClient);

    }

    public function setHttpProxy($httpProxy = null)
    {
        curl_setopt($this->httpClient, CURLOPT_PROXY, ($httpProxy ? $httpProxy : $this->getConfig()->httpProxy);
        parent::setHttpProxy($httpProxy);
    }

    public function setRequest($method, $args = array())
    {
        $request = array (
                                'method'  => $method,
                                'params'  => $args,
                                'id'      => 0,
                                'jsonrpc' => '2.0'
                           );

        $this->request = json_encode($request);
    }

    public function sendRequest()
    {
        curl_setopt($this->httpClient, CURLOPT_POSTFIELDS, $this->getRequest());
        $response = curl_exec($this->httpClient);

        if (empty($response)) {
            $this->setError(array(
                                    'code' => curl_errno($this->httpClient),
                                    'message' => curl_error($this->httpClient)
                            ));
            throw new Exception('The API returned an empty response.', 'HTTP_REQUEST_FAILED');
        }

        // We have a response!
        $response = json_decode($response);
        $this->setResponse($response);

        // Is it a valid json response?
        $lastJsonErrorCode = json_last_error();
        if ($lastJsonErrorCode) {
            $this->setError(array(
                                    'code' => $lastJsonErrorCode,
                                    'message' => (function_exists('json_last_error_msg') ? json_last_error_msg() : null)
                ));
            throw new Exception('Json decode has failed.', 'JSON_RESPONSE_ERROR');
        }

        if (isset($this->getResponse()->result)) {
            return true;
        } else {
            throw new Exception('The response result is empty.', 'JSON_RESPONSE_ERROR');
        }

    }

    /**
     * Magic method that is used as an interface between our model classes
     * and API.
     *
     * Example: $apiClient->call_getSomeStuff($key);
     *          The API receives the 'getSomeStuff' method request.
     *
     * @param  string $methodName
     * @param  array $args
     * @return [type]
     */
    public function __call($methodName, $args)
    {
        if (substr($methodName, 0, 5) != 'call_') {
            throw new \BadMethodCallException('Inexistent method: ' . $methodName, 'UNKNOWN_METHOD_CALL');
        }
        $methodName = substr($methodName, 5);
        $this->setRequest($methodName, $args);
        $this->sendRequest();

        return $this->getResponse()->result;

    }

    public function login()
    {

    }

    public function logout()
    {

    }
}
