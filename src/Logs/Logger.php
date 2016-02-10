<?php
namespace Lorenum\Arcane\Logs;

class Logger{
    protected $logs = [];
    protected $lastInsertID;
    protected $storage;

    public function __construct(LoggerStorageInterface $storage) {
        $this->storage = $storage;
        $this->lastInsertID = date('Y-m-d H:i:s');
    }

    public function addLog($context, $log){
        $currentTimeStamp = date('Y-m-d H:i:s');

        if(is_object($log))
            $log = serialize($log);

        $this->logs[][] = [
            "context"   => $context,
            "timestamp" => $currentTimeStamp,
            "log"       => $log,
            "logDiff"   => strtotime($currentTimeStamp) - strtotime($this->lastInsertID)
        ];

        $this->lastInsertID = $currentTimeStamp;
    }

    public function getLogs(){
        return $this->logs;
    }

    public function store(){
        return $this->storage->save(json_encode($this->logs));
    }
}