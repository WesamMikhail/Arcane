<?php
namespace Example\Procedures;

use Lorenum\Arcane\Planner\Tasks\ProcedureRunnerTask\AbstractProcedure;

Class UserProcedures extends AbstractProcedure{
    public function get(){
        $this->response->setMessage("Success");
    }
}
