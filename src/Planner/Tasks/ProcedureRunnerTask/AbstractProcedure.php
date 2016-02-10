<?php
namespace Lorenum\Arcane\Planner\Tasks\ProcedureRunnerTask;

use Lorenum\Arcane\Database\ConnectionContainer;
use Lorenum\Arcane\Logs\Logger;
use Lorenum\Arcane\Request\Request;
use Lorenum\Arcane\Response\Response;

abstract class AbstractProcedure implements ProcedureInterface{
    protected $request;
    protected $response;
    protected $dbContainer;
    protected $logger;

    public function __construct(Request $request, Response $response, ConnectionContainer $dbContainer, Logger $logger) {
        $this->request = $request;
        $this->response = $response;
        $this->dbContainer = $dbContainer;
        $this->logger = $logger;
    }
}