<?php
namespace Client;

interface ClientInterface
{
    public function __construct($options, \Transport\TransportInterface $transport);
    public function setOptions($options);
    public function getOptions();
    public function setTransport(\Transport\TransportInterface $transport);
    public function getTransport();
    public function setHost();
    public function getHost();
    public function setPort();
    public function getPort();
    public function setProxy();
    public function getProxy();
    public function send($method, $args);
    public function login();
    public function logout();
}
