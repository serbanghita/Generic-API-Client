<?php
namespace GenericApiClient\Transport;

class Socket implements TransportInterface
{

    protected $secure;
    protected $host;
    protected $path;
    protected $port;
    protected $timeout;
    protected $headers;
    protected $proxy;
    protected $persistent;

    private $handler;

    protected $requestHeaders;
    protected $requestBody;
    protected $responseHeaders;
    protected $responseBody;

    public function __construct()
    {
    }

    private static function normalizeHeaders($headersArray = array())
    {
        if (!is_array($headersArray) || empty($headersArray)) {
            return false;
        }

        $result = '';
        foreach ($headersArray as $headerName => $headerValue) {
            $result .= $headerName . ': ' . $headerValue . "\r\n";
        }

        return $result;
    }

    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function getResponseBody()
    {
        return $this->responseBody;
    }

    public function getSecure()
    {
        return $this->secure;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getProxy()
    {
        return $this->proxy;
    }

    public function setPersistent($persistent)
    {
        $this->persistent = $persistent;
    }

    public function getPersistent()
    {
        return $this->persistent;
    }

    public function connect($uri, $timeout = 10, $headers = array(), $proxy = '', $persistent = null)
    {
        if (!isset($uri) || empty($uri)) {
            throw new \InvalidArgumentException('Invalid URI passed.');
        }

        $uriArr        = parse_url($uri);
        $this->secure  = $uriArr['scheme'] == 'https' ? true : false;
        $this->host    = $uriArr['host'];
        $this->port    = isset($uriArr['port']) ? $uriArr['port'] : 80;
        $this->path    = isset($uriArr['path']) ? $uriArr['path'] : '/';
        $this->headers = $headers;
        $this->proxy   = $proxy;
        $this->persistent = isset($persistent) ? $persistent : $this->persistent;
        $flags = STREAM_CLIENT_CONNECT;
        if ($this->persistent) {
            $flags |= STREAM_CLIENT_PERSISTENT;
        }

        if (empty($this->host)) {
            throw new \InvalidArgumentException('Invalid URI passed.');
        }

        // Create the stream context
        $context = stream_context_create();

        // Apply stream options
        stream_context_set_option($context, 'http', 'timeout', $timeout);
        stream_context_set_option($context, 'http', 'header', $this->normalizeHeaders($headers));
        stream_context_set_option($context, 'http', 'follow_location', true);
        stream_context_set_option($context, 'http', 'max_redirects', 1);
        stream_context_set_option($context, 'http', 'proxy', $proxy);

        $this->handler = stream_socket_client(
                                                $this->host . ':' . $this->port,
                                                $errno,
                                                $errstr,
                                                $timeout,
                                                $flags,
                                                $context
                                            );
        if (!$this->handler) {
            throw new \RuntimeException($errstr, $errno);
        }

        return true;

    }

    public function send($method, $path = null, $requestBody = '')
    {
        if (!$this->handler) {
            throw new \RuntimeException('Trying to write but no connection was done.');
        }

        if (!isset($path)) {
            $path = $this->path;
        }

        $request =  $method . ' '. $path . ' HTTP/1.1' . "\r\n";
        $request .= 'Host: ' . $this->host . "\r\n";
        $request .= $this->normalizeHeaders($this->headers);
        $request .= "\r\n";
        $this->requestHeaders = $request;

        $request .= $requestBody;
        $this->requestBody = $requestBody;

        $send = fwrite($this->handler, $request);
        if ($send === false) {
            throw new \RuntimeException('Could not write the request.');
        }

        $currentPos =  ftell($this->handler);

        var_dump($currentPos);

        $response = null;
        $gotResponseBody = false;
        while (!feof($this->handler)) {
            $line = fread($this->handler, 1024);
            $response .= $line;
            $line = rtrim($line);
            if (empty($line)) {
                if ($gotResponseBody) {
                    break;
                } else {
                    $gotResponseBody = true;
                }
            }
        }

        echo "\n\n";
        var_dump($response);
        echo "\n\n";

        $responseArr           = explode("\r\n\r\n", $response);

        $this->responseHeaders = $responseArr[0];
        $this->responseBody    = isset($responseArr[1]) ? $responseArr[1] : null;
        if ( $this->persistent && !empty($this->responseBody)) {
            $responseBodyArr = explode("\r\n", $this->responseBody);
            $this->responseBody = $responseBodyArr[1];
        }

        return true;
    }

    public function close()
    {
        fclose($this->handler);
    }
}
