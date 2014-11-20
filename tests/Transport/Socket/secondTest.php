<?php
namespace GenericApiClientTest\Transport\Socket;

use GenericApiClient\Transport as T;

class SocketSecondTest extends \PHPUnit_Framework_TestCase
{

    /**
     * connecting to an empty host throws exception
     * @expectedException \InvalidArgumentException
     */
    public function testConnectingToAnEmptyHostThrowsException()
    {
        $uri = '';
        $transport = new T\Socket;
        $result = $transport->connect($uri);
    }

    /**
     * connecting to a valid URI returns all expected components
     */
    public function testConnectingToAValidUriReturnsAllExpectedComponents()
    {
        $uri = 'https://demo.mobiledetect.net:80/test/json_simple.php';
        $timeout = 1;
        $headers = array(
            'Accept' => 'text/plain',
            'User-Agent' => 'Generic API Client',
            );
        $proxy = 'http://proxy.boss.com:1234';
        $transport = new T\Socket;
        $result = $transport->connect($uri, $timeout, $headers, $proxy);

        $this->assertTrue($transport->getSecure());
        $this->assertSame('demo.mobiledetect.net', $transport->getHost());
        $this->assertSame(80, $transport->getPort());
        $this->assertSame($headers, $transport->getHeaders());
        $this->assertSame($proxy, $transport->getProxy());
    }

    /**
     * normalizing headers outputs the expected HTTP headers
     */
    public function testNormalizingHeadersOutputsTheExpectedHttpHeaders()
    {
        $headers = array(
            'Accept'           => 'text/plain',
            'Content-Type'     => 'application/json',
            'User-Agent'       => 'Generic API Client',
            'X-Requested-With' => 'XMLHttpRequest'
            );
        $expectedHeaders = 'Accept: text/plain' . "\r\n" .
                           'Content-Type: application/json' . "\r\n" .
                           'User-Agent: Generic API Client' . "\r\n" .
                           'X-Requested-With: XMLHttpRequest' . "\r\n";
        $transport = new T\Socket;

        $class   = new \ReflectionClass(get_class($transport));
        $method = $class->getMethod('normalizeHeaders');
        $method->setAccessible(true);
        $result = $method->invokeArgs($transport, array($headers));

        $this->assertSame($result, $expectedHeaders);

    }



    /**
     * connecting to a valid host returns true
     */
    public function testConnectingToAValidHostReturnsTrue()
    {
        $uri = 'http://demo.mobiledetect.net/test/json_simple.php';
        $transport = new T\Socket;
        $result = $transport->connect($uri);

        $this->assertTrue($result);
    }

    /**
     * sending a simple valid GET request returns true
     */
    public function testSendingASimpleValidGetRequestReturnsTrue()
    {
        $uri = 'http://demo.mobiledetect.net/test/json_simple.php';
        $transport = new T\Socket;
        $transport->connect($uri);
        $result = $transport->send('GET');

        $this->assertTrue($result);
    }

    /**
     * sending a valid GET request returns the expected response body
     */
    public function testSendingAValidGetRequestReturnsTheExpectedResponseBody()
    {
        $uri = 'http://demo.mobiledetect.net/test/json_simple.php';
        $transport = new T\Socket;
        $transport->connect($uri);
        $result = $transport->send('GET');
        $expectedResponseBody = '{"status":"success","msg":"Thank you!"}';

        $this->assertTrue($result);
        $this->assertNotEmpty($transport->getResponseHeaders());
        $this->assertEquals($transport->getResponseBody(), $expectedResponseBody);
    }

    /**
     * sending two consecutive GET requests on a persistent connection return the expected response bodies
     */
    public function testSendingTwoConsecutiveGetRequestsOnAPersistentConnectionReturnTheExpectedResponseBodies()
    {
        $uri = 'http://demo.mobiledetect.net';
        $transport = new T\Socket;
        $transport->setPersistent(true);
        $transport->connect($uri);

        $result = $transport->send('GET', '/test/json_simple.php');
        $expectedResponseBody = '{"status":"success","msg":"Thank you!"}';
        $this->assertEquals($transport->getResponseBody(), $expectedResponseBody);
        //var_dump($transport->getRequestHeaders());

        //$result = $transport->send('GET', '/test/json_simple2.php');
        //$expectedResponseBody = '{"status":"success","msg":"Thank you again!"}';
        //$this->assertEquals($transport->getResponseBody(), $expectedResponseBody);
        //var_dump($transport->getRequestHeaders());
    }
}
