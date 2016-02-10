<?php
namespace Lorenum\Arcane\Planner\Tasks\ProcedureRunnerTask;

use Lorenum\Arcane\Database\ConnectionContainer;
use Lorenum\Arcane\Logs\Logger;
use Lorenum\Arcane\Request\Request;
use Lorenum\Arcane\Response\Response;

Interface ProcedureInterface{
    public function __construct(Request $request, Response $response, ConnectionContainer $dbContainer, Logger $logger);
}