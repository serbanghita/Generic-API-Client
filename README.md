Generic API Client
==================

A generic API client with basic HTTP support written in PHP.

* HTTP transport via `cURL` or `sockets`
* Support for `JSON-RPC` or `REST` API endpoints

```php
$options = array(
        'uri' => 'http://www.raboof.com/projects/jayrock/demo.ashx',
        'jsonrpc' => '2.0'
    );
$client = new \Client\JsonRPC($options);
$response = $client->call_add(1, 2);
var_dump($response);
```

### Changelog

* `1.0.0` - First beta version, supports `JSON-RPC` APIs and `cURL` transfer. 
