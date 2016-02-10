<?php
namespace Lorenum\Arcane\Errors\HTTPExceptions;

use Exception;

/**
 * Class HTTPException
 * Base user facing Exception class for Arcane framework
 *
 * @package Lorenum\Arcane\Errors\HTTPExceptions
 */
class HTTPException extends Exception{
    protected $status;

    public function __construct($status, $code, $message = ''){
        $this->status = $status;
        parent::__construct($message, $code);
    }

    public function getStatus() {
        return $this->status;
    }
}