<?php
namespace GenericApiClientTest\Transport\Socket;

use GenericApiClient\Transport as T;

class SocketFirstTest extends \PHPUnit_Framework_TestCase
{

    /**
     * get options returns the options set by the constructor
     */
    public function testGetOptionsReturnsTheOptionsSetByTheConstructor()
    {
        $options = array(
           'http_user_agent' => 'Mozilla',
           'http_timeout' => 30
        );
        $transport = new T\Socket($options);
        $this->assertEquals($options, $transport->getOptions());
    }

    /**
     * initializing the constructor with empty options throws an exception
     * @expectedException \InvalidArgumentException
     */
    public function testInitializingTheConstructorWithEmptyOptionsThrowsAnException()
    {
        $options = null;
        $transport = new T\Socket($options);
    }

    /**
     * initializing the constructor with an empty options array throws an exception
     * @expectedException \InvalidArgumentException
     */
    public function testInitializingTheConstructorWithAnEmptyOptionsArrayThrowsAnException()
    {
        $options = array();
        $transport = new T\Socket($options);
    }

    /**
     * initializing the constructor with unknown options array throws an exception
     * @expectedException \InvalidArgumentException
     */
    public function testInitializingTheConstructorWithUnknownOptionsArrayThrowsAnException()
    {
        $options = array(
            'unknownOptionNameOne' => 'First value',
            'unknownOptionNameTwo' => 'Second value',
            '' => 'Third value'
        );
        $transport = new T\Socket($options);
    }



    /**
     * get request gets the previously set request
     */
    public function testGetRequestGetsThePreviouslySetRequest()
    {
        $options = array(
           'http_user_agent' => 'Mozilla',
           'http_timeout' => 30
        );
        $transport = new T\Socket($options);
        $requestString = 'This is a request';
        $transport->setRequest($requestString);
        $this->assertEquals($requestString, $transport->getRequest());
    }


}
