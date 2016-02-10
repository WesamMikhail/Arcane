<?php
namespace Lorenum\Arcane\Errors;


use Exception;
use Lorenum\Arcane\Errors\HTTPExceptions\HTTPException;
use Lorenum\Arcane\Errors\HTTPExceptions\HTTPException_500;
use Lorenum\Arcane\Logs\LoggerFileStorage;

use Pimple\Container;

/**
 * Class ErrorHandler
 * This class is a static function container that allows us to register expected as well as unexpected error handlers.
 * In production mode you will most likely want your errors to be displayed in a safely manner.
 * This class contains a bunch of static functions that will allow you to do just that.
 *
 * @package Lorenum\Arcane\Errors
 */
class ErrorHandler{
    public static function registerErrorHandlers(Container $container){
        if($container["configs"]->getConfigsByKey("globals")->environment == "production") {
            set_error_handler(function ($errno, $errstr, $errfile, $errline) {
                throw new HTTPException_500;
            });

            set_exception_handler(function (Exception $e) use ($container) {
                $code = 500;
                $status = "Internal Server Error";
                $message = 'Please contact an administrator about this error';

                if ($e instanceof HTTPException) {
                    $code = $e->getCode();
                    $status = $e->getStatus();
                    $message = $e->getMessage();
                } else {
                    $message .= " - Internal Code: " . $e->getCode();
                }

                $response = $container["response"];
                $response->setProtocol($container["request"]->getProtocol());
                $response->setCode($code);
                $response->setStatus($status);
                $response->setMessage($message);

                if (isset($container["logger"])) {
                    $container["logger"]->addLog("Response", $response);
                    $container["logger"]->store(new LoggerFileStorage("logs"));
                }
                $response->send();
            });

            register_shutdown_function(function () use ($container) {
                $error = error_get_last();

                if ($error !== null) {
                    $response = $container["response"];
                    $response->setProtocol($container["request"]->getProtocol());
                    $response->setCode(500);
                    $response->setStatus("Internal Server Error");
                    $response->setMessage("Unexpected error caused a fatal shutdown. Please contact the server administrator about this problem");

                    if (isset($container["logger"])) {
                        $container["logger"]->addLog("Response", $response);
                        $container["logger"]->store(new LoggerFileStorage("logs"));
                    }
                    $response->send();
                }
            });
        }
    }
}