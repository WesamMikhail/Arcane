<?php
namespace Lorenum\Arcane\Planner\Tasks\ProcedureRunnerTask;

use Lorenum\Arcane\Errors\ApplicationExceptions\InstanceException;
use Lorenum\Arcane\Errors\HTTPExceptions\HTTPException_500;
use Lorenum\Arcane\Planner\Tasks\TaskInterface;
use Pimple\Container;

class ProcedureRunnerTask implements TaskInterface{

    public function execute(Container $container) {
        //If we are trying to run a procedure while no root is defined
        $route = $container["route"];
        if(is_null($route))
            throw new HTTPException_500("Server could not determine the appropriate route for your request.");

        $procedure = explode("@", $route->getProcedure());

        if(count($procedure) != 2 || !method_exists($procedure[0], $procedure[1]))
            throw new InstanceException("Route procedure cannot be found.");

        if(!class_implements($procedure[0], "\\Lorenum\\Arcane\\Planner\\Tasks\\ProcedureRunnerTask\\ProcedureInterface"))
            throw new InstanceException("The route procedure must implement \\Lorenum\\Arcane\\Planner\\Tasks\\ProcedureRunnerTask\\ProcedureInterface");

        //Run procedure
        $object = new $procedure[0]($container["request"], $container["response"], $container["DBContainer"], $container["logger"]);
        $object->{$procedure[1]}();
    }
}