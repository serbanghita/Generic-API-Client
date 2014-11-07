<?php
namespace Transport;

class Curl extends AbstractTransport
{

    public function __construct($options)
    {
        if (!extension_loaded('curl')) {
            throw new Exception('cURL extension has to be loaded.', 'API_CLIENT_CURL_MISSING');
        }

        $this->setOptions($options);

    }

    public function setOptions($options)
    {
        // Default cURL options.
        $defaultOptions = array(
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Accept: application/json'
                //'Content-Type: text/plain',
                //'Accept: text/plain'
            ),
            // Set a proxy for the connection.
            CURLOPT_PROXY => '',
            // Check remote certificate's validity.
            CURLOPT_SSL_VERIFYPEER => true,
            // So we can catch explicit curl errors.
            CURLOPT_VERBOSE => true
        );

        // Add the default option to the specified ones.
        // Save the options.
        $this->options = $defaultOptions + $options;

    }

    public function connect($host, $port = 80)
    {
        $this->handler = curl_init();

        // Apply the options.
        $options = $this->getOptions();
        foreach ($options as $optionName => $optionValue) {
            curl_setopt($this->handler, $optionName, $optionValue);
        }

    }

    public function write($method, $uri, $headers, $body)
    {
        switch ($method) {
            case 'GET':
                curl_setopt($this->handler, CURLOPT_HTTPGET, true);
                break;
            case 'POST':
                curl_setopt($this->handler, CURLOPT_POSTFIELDS, $body);
                break;
        }

        // Save the request.
        $request  = curl_getinfo($this->handler, CURLINFO_HEADER_OUT);
        $request .= $body;
        $this->setRequest($request);

        // Save the response.
        $response = curl_exec($this->handler);
        $this->setResponse($response);

        if (empty($response)) {
            throw new Exception(
                'The request returned an empty response. ' .
                ' Code: ' . curl_errno($this->handler) .
                ' Message: ' . curl_error($this->handler),
                'HTTP_REQUEST_FAILED'
            );
        }

        return true;

    }

    public function read()
    {
        return $this->getResponse();
    }

    public function close()
    {
        if (is_resource($this->handler)) {
            curl_close($this->handler);
        }

        $this->handler = null;

    }
}
