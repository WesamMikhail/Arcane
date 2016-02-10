<?php
if(is_readable('vendor/autoload.php'))
    require_once 'vendor/autoload.php';

use \Lorenum\Arcane\Configs\Configs;
use \Lorenum\Arcane\Logs\Logger;
use \Lorenum\Arcane\Response\JSONResponse;
use \Lorenum\Arcane\Request\Request;
use \Lorenum\Arcane\Planner\ExecutionPlan;
use \Lorenum\Arcane\Database\ConnectionContainer;
use \Lorenum\Arcane\Logs\LoggerFileStorage;
use Pimple\Container;

$container = new Container();
$container["configs"]       = function($self){ return new Configs("./configs.json"); };
$container["logger"]        = function($self){ return new Logger(new LoggerFileStorage("logs")); };
$container["request"]       = function($self){ return Request::parseFromGlobals(); };
$container["response"]      = function($self){ return new JSONResponse(); };
$container["DBContainer"]   = function($self){ return new ConnectionContainer(); };

\Lorenum\Arcane\Errors\ErrorHandler::registerErrorHandlers($container);

$container["logger"]->addLog("Request", $container["request"]);

$planner = new ExecutionPlan($container);
$planner->addTask("DBConnection",       new \Lorenum\Arcane\Planner\Tasks\DBConnectionTask\DBConnectionTask());
$planner->addTask("Routing",            new \Lorenum\Arcane\Planner\Tasks\RoutingTask\RoutingTask());
$planner->addTask("ProcedureRunner",    new \Lorenum\Arcane\Planner\Tasks\ProcedureRunnerTask\ProcedureRunnerTask());
$planner->run();

$container["logger"]->addLog("Response", $container["response"]);
$container["logger"]->store();

$container["response"]->send();