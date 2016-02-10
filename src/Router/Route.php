<?php
namespace Lorenum\Arcane\Router;

class Route{
    protected $procedure;
    protected $params = [];
    protected $properties;

    /**
     * @return string
     */
    public function getProcedure() {
        return $this->procedure;
    }

    /**
     * @param string $procedure
     */
    public function setProcedure($procedure) {
        $this->procedure = $procedure;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams($params) {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getProperties() {
        return $this->properties;
    }

    /**
     * @param mixed $properties
     */
    public function setProperties($properties) {
        $this->properties = $properties;
    }

}