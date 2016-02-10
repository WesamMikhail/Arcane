<?php
namespace Lorenum\Arcane\Planner;


use Lorenum\Arcane\Errors\ApplicationExceptions\ArgumentException;
use Lorenum\Arcane\Errors\ApplicationExceptions\InstanceException;
use Lorenum\Arcane\Planner\Tasks\TaskInterface;
use Pimple\Container;

class ExecutionPlan{
    protected $container;
    protected $tasks = [];

    public function __construct(Container $container) {
        $this->container = $container;
    }

    public function addTask($id, $task){
        if(empty($id))
            throw new ArgumentException("Task must have an ID specified.");

        if(!is_subclass_of($task, "\\Lorenum\\Arcane\\Planner\\Tasks\\TaskInterface"))
            throw new InstanceException("The task by ID: '$id' must be of type \\Lorenum\\Arcane\\Planner\\Tasks\\TaskInterface");

        $this->tasks[] = [
            "id"        => $id,
            "pointer"   => $task
        ];
    }

    public function run(){
        $this->container["logger"]->addLog(__CLASS__, "Execution Planner started running.");
        foreach($this->tasks as $task){
            $this->container["logger"]->addLog(__CLASS__, "Task '{$task["id"]}' started running.");
            $task["pointer"]->execute($this->container);
            $this->container["logger"]->addLog(__CLASS__, "Task '{$task["id"]}' finished running.");
        }
        $this->container["logger"]->addLog(__CLASS__, "Execution Planner finished running.");
    }
}