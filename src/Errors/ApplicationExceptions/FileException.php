<?php
namespace Lorenum\Arcane\Errors\ApplicationExceptions;

use Exception;

/**
 * Class FileException
 * To be used when a file is trying to be reached but cannot be read/opened
 *
 * @package Lorenum\Arcane\Errors\ApplicationExceptions
 */
class FileException extends Exception{
    public function __construct($message = 'Cannot open/read specified file.', $code = 500){
        parent::__construct($message, 500);
    }
}