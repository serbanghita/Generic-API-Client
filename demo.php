<?php
include_once 'lib/Client/Exception.php';
include_once 'lib/Client/AbstractClient.php';
include_once 'lib/Client/JsonRPC.php';
include_once 'lib/Transport/Exception.php';
include_once 'lib/Transport/AbstractTransport.php';
include_once 'lib/Transport/Curl.php';

$options = array(
        'uri' => 'http://www.raboof.com/projects/jayrock/demo.ashx',
        'jsonrpc' => '2.0'
    );

$client = new \Client\JsonRPC($options);
$response = $client->call_add(1, 2);
var_dump($response);
