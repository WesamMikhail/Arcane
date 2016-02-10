<?php
namespace Lorenum\Arcane\Configs;

use Lorenum\Arcane\Errors\ApplicationExceptions\FileException;

final class Configs{
    private $file;

    public function __construct($file){
        if(!is_readable($file))
            throw new FileException;

        $file = json_decode(file_get_contents($file));

        if(is_null($file))
            throw new FileException;

        $this->file = $file;
    }

    public function getConfigs(){
        return $this->file;
    }

    public function getConfigsByKey($key){
        if(isset($this->file->$key))
            return $this->file->$key;
        return null;
    }
}