<?php
namespace Lorenum\Arcane\Planner\Tasks\DBConnectionTask;

use Lorenum\Arcane\Errors\ApplicationExceptions\ConfigurationException;
use Lorenum\Arcane\Planner\Tasks\TaskInterface;
use Pimple\Container;

class DBConnectionTask implements TaskInterface{

    public function execute(Container $container) {
        $dbc = $container["DBContainer"];

        //If no DBconnections are in configurations we just return to end the function while the container remains empty!
        if(is_null($container["configs"]->getConfigsByKey("globals")->db_connections))
            return;

        foreach($container["configs"]->getConfigsByKey("globals")->db_connections as $name => $connection){
            if(!isset($connection->driver))
                throw new ConfigurationException("DB configuration missing 'driver' key.");

            if(!isset($connection->host))
                throw new ConfigurationException("DB configuration missing 'host' key.");

            if(!isset($connection->user))
                throw new ConfigurationException("DB configuration missing 'user' key.");

            if(!isset($connection->password))
                throw new ConfigurationException("DB configuration missing 'password' key.");

            if(!isset($connection->database))
                throw new ConfigurationException("DB configuration missing 'database' key.");

            $dbc->add($name, $dbc::createConnection($connection->driver,$connection->host, $connection->database, $connection->user, $connection->password));
        }
    }
}