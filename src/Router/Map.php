<?php
namespace Lorenum\Arcane\Router;

class Map{
    const METHOD_GET = "GET";
    const METHOD_POST = "POST";
    const METHOD_PUT = "PUT";
    const METHOD_DELETE = "DELETE";

    protected $nodes;

    function __construct(){
        $this->nodes = new Node();
    }

    public function add($method, $route, $resource, $properties){
        $method = strtolower($method);
        $route = trim($route, "/");
        $fragments = explode("/", $route);

        //Root is defined as / instead of empty space
        if(count($fragments) === 1 && $fragments[0] == ""){
            $fragments[0] = "/";
        }

        $parent = $this->nodes;

        foreach($fragments as $piece){
            if($node = $parent->getChild($piece)){
                $parent = $node;
            }
            else{
                $node = new Node();
                $node->setFragment(strtolower($piece));
                $parent->addChild($node);
                $parent = $node;
            }
        }

        $parent->addRoute($method, $resource, $properties);
    }

    public function match($method, $route){
        $method = strtolower($method);
        if(!is_array($route)){
            $route = trim($route, "/");
            $route = explode("/", $route);

            //Root is defined as / instead of empty space
            if(count($route) === 1 && $route[0] == ""){
                $route[0] = "/";
            }
        }
        //If the route is left empty, we assume that it is ROOT
        else if(empty($route)){
            $route = ["/"];
        }

        $node = $this->nodes;
        $params = [];

        foreach($route as $fragment){
            //Search for fragment as pre-defined piece of the URI
            $child = $node->getChild(strtolower($fragment));
            if($child === false){

                //Search for fragment as a parameter of the URI
                $child = $node->getChild(":");
                if($child === false){
                    return false;
                }

                $params[] = $fragment;
            }

            $node = $child;
        }

        $routes = $node->getRoutes();
        if(isset($routes[$method])){
            $route = new Route();
            $route->setProcedure($routes[$method]["target"]);
            $route->setParams($params);
            $route->setProperties($routes[$method]["properties"]);

            return $route;
        }
        else if(count($routes) > 0)
            return true;
        else
            return false;
    }
}