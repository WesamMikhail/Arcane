<?php
namespace Lorenum\Arcane\Errors\ApplicationExceptions;

use Exception;

/**
 * Class ArgumentException
 * To be used when a function argument is not of expected value or when a value is not expected type
 *
 * @package Lorenum\Arcane\Errors\ApplicationExceptions
 */
class ConfigurationException extends Exception{
    public function __construct($message = 'Invalid configuration file content.', $code = 500){
        parent::__construct($message, 500);
    }
}