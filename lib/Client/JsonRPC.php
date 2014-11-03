<?php
namespace Client;

class JsonRPC extends AbstractClient
{

    public function send($method, $args = array())
    {

        $options = $this->getOptions();

        // Compose the request.
        $request = array (
                                'method'  => $method,
                                'params'  => $args,
                                'id'      => 0,
                                'jsonrpc' => $options['jsonrpc']
                           );

        $request = json_encode($request);

        // Send the request.
        try {
            $this->transport->connect($this->getHost(), $this->getPort());
            $this->transport->write('POST', $options['uri'], null, $request);
        } catch (\Transport\Exception $e) {
             throw new Exception('Request failed.', 'REQUEST_FAILED', $e);
        }

        // We have a response! Read the response.

        $response = json_decode($this->transport->read());

        // Is it a valid json response?
        $lastJsonErrorCode = json_last_error();
        if ($lastJsonErrorCode) {
            throw new Exception('Json decode has failed. Code: ' . $lastJsonErrorCode, 'RESPONSE_ERROR');
        }

        if (isset($response->result)) {
            return $response->result;
        } else {
            throw new Exception('The response result is empty.', 'RESPONSE_ERROR');
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
        $result = $this->send($methodName, $args);

        return $result;

    }

    public function login()
    {

    }

    public function logout()
    {

    }
}
