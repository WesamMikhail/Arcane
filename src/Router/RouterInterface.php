<?php
namespace Lorenum\Arcane\Router;

use Lorenum\Arcane\Request\Request;

/**
 * Interface RouterInterface
 * This interface must be implemented in order for a router to be classified as such
 *
 * @package Lorenum\Arcane\Routers
 */
Interface RouterInterface{

    /**
     * @param Request $request
     * @return Route|boolean
     */
    public function match(Request $request);
}