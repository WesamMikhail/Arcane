<?php
namespace Lorenum\Arcane\Database;

use Lorenum\Arcane\Errors\ApplicationExceptions\ArgumentException;
use PDO;

class ConnectionContainer{
    protected $instances = [];

    public function add($key, PDO $instance){
        $this->instances[$key] = $instance;
    }

    public function get($key){
        if(isset($this->instances[$key]))
            return $this->instances[$key];

        throw new ArgumentException("The key $key does not exist.");
    }

    public function getAll(){
        return $this->instances;
    }

    public static function createConnection($driver, $host, $db, $user, $password, array $options = []){
        $db = new PDO("$driver:dbname=$db;host=$host", $user, $password);

        if(!isset($options[PDO::ATTR_DEFAULT_FETCH_MODE]))
            $options[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_OBJ;

        foreach($options as $key => $val){
            $db->setAttribute($key, $val);
        }

        return $db;
    }
}