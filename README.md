Generic-API-Client
==================

A generic API client with basic HTTP support written in PHP.

```php
$options = array(
        'uri' => 'http://www.raboof.com/projects/jayrock/demo.ashx',
        'jsonrpc' => '2.0'
    );
$client = new \Client\JsonRPC($options);
$response = $client->call_add(1, 2);
var_dump($response);
```
