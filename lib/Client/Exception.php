<?php
namespace Client;

class Exception extends \Exception
{

    protected $message = null;
    protected $code = null;

    public function __construct($message, $code, $previous = null)
    {

        $this->message = $message;
        $this->code = $code;

        parent::__construct($message, null, $previous);

    }
}
