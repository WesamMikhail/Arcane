<?php
namespace Lorenum\Arcane\Planner\Tasks;

use Pimple\Container;

Interface TaskInterface{
    public function execute(Container $container);
}
