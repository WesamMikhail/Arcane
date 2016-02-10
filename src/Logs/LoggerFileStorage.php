<?php
namespace Lorenum\Arcane\Logs;

use Lorenum\Arcane\Errors\ApplicationExceptions\ArgumentException;

class LoggerFileStorage implements LoggerStorageInterface{
    protected $file;

    public function __construct($file) {
        if(empty($file))
            throw new ArgumentException("File cannot be empty.");

        $this->file = $file;
    }

    public function save($data) {
        return file_put_contents($this->file, $data . PHP_EOL, FILE_APPEND | LOCK_EX );
    }
}