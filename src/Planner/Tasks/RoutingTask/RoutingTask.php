<?php
namespace Lorenum\Arcane\Planner\Tasks\RoutingTask;

use Lorenum\Arcane\Errors\ApplicationExceptions\ArgumentException;
use Lorenum\Arcane\Errors\ApplicationExceptions\ConfigurationException;
use Lorenum\Arcane\Errors\HTTPExceptions\HTTPException_404;
use Lorenum\Arcane\Errors\HTTPExceptions\HTTPException_405;
use Lorenum\Arcane\Planner\Tasks\TaskInterface;
use Lorenum\Arcane\Router\Map;
use Lorenum\Arcane\Router\Route;
use Pimple\Container;


class RoutingTask implements TaskInterface{

    public function execute(Container $container) {
        $map = new Map;

        $routes = $container["configs"]->getConfigsByKey("routes");
        if(is_null($routes))
            throw new ConfigurationException("Routes are missing from the configuration file.");

        foreach($routes as $route){
            if(!isset($route->path) || !is_string($route->path))
                throw new ConfigurationException("Every route must have a path (string).");

            if(!isset($route->method) || !is_string($route->method))
                throw new ConfigurationException("Every route must have a method (string).");

            if(!isset($route->procedure) || !is_string($route->procedure))
                throw new ConfigurationException("Every route must have a procedure (string).");

            if(!isset($route->properties))
                $route->properties = null;

            $map->add($route->method, $route->path, $route->procedure, $route->properties);
        }

        $route = $map->match($container["request"]->getMethod(), $container["request"]->getUri());

        if($route instanceof Route) {
            $container["route"] = $route;
            $container["request"]->setParams($route->getParams());
        }
        else if($route === true)
            throw new HTTPException_405;
        else
            throw new HTTPException_404;
    }
}