<?php
namespace Lorenum\Arcane\Request;

use Lorenum\Arcane\Configs\Pool;
use Lorenum\Arcane\Errors\HTTPExceptions\HTTPException_400;

/**
 * Class Request
 * This class substitutes the need for using the super-globals such as $_SERVER, $_GET, $_POST, etc.
 * All the information you might need from the super globals can be found in this object including URI parsing.
 *
 * @package Lorenum\Arcane\Request
 */
class Request{
    protected $method;
    protected $protocol;
    protected $domain;

    /**
     * @var mixed the path that is common between $uri and $script.
     */
    protected $path;

    protected $uri;
    protected $script;
    protected $ip;
    protected $query = [];
    protected $body = [];
    protected $headers = [];


    /**
     * Get a single query string value by its key
     *
     * @param $key
     * @return mixed|null null on failure to find or if the key itself has null value
     */
    public function query($key){
        if(isset($this->query[$key]))
            return $this->query[$key];

        return null;
    }

    /**
     * Get a single body content value by its key
     *
     * @param $key
     * @return mixed|null null on failure to find or if the key itself has null value
     */
    public function body($key){
        if(isset($this->body[$key]))
            return $this->body[$key];

        return null;
    }

    /**
     * Get a single header value by its key
     *
     * @param $key
     * @return mixed|null null on failure to find or if the key itself has null value
     */
    public function header($key){
        if(isset($this->headers[$key]))
            return $this->headers[$key];

        return null;
    }

    /**
     * @return mixed
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * @param mixed $method
     */
    public function setMethod($method) {
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * @param mixed $protocol
     */
    public function setProtocol($protocol) {
        $this->protocol = $protocol;
    }

    /**
     * @return mixed
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * @param mixed $domain
     */
    public function setDomain($domain) {
        $this->domain = $domain;
    }

    /**
     * @return mixed
     */
    public function getUri() {
        return $this->uri;
    }

    /**
     * @param mixed $uri
     */
    public function setUri($uri) {
        $this->uri = $uri;
    }

    /**
     * @return mixed
     */
    public function getScript() {
        return $this->script;
    }

    /**
     * @param mixed $script
     */
    public function setScript($script) {
        $this->script = $script;
    }

    /**
     * @return mixed
     */
    public function getIp() {
        return $this->ip;
    }

    /**
     * @param mixed $ip
     */
    public function setIp($ip) {
        $this->ip = $ip;
    }

    /**
     * @return array
     */
    public function getQuery() {
        return $this->query;
    }

    /**
     * @param array $query
     */
    public function setQuery($query) {
        $this->query = $query;
    }

    /**
     * @return array
     */
    public function getBody() {
        return $this->body;
    }

    /**
     * @param array $body
     */
    public function setBody($body) {
        $this->body = $body;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers) {
        $this->headers = $headers;
    }

    /**
     * @return mixed
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path) {
        $this->path = $path;
    }


    public static function getGlobalHeaders(){
        $headers = array();

        if(function_exists('getallheaders')){
            foreach(getallheaders() as $key => $value) {
                $key = str_replace(" ", "_", $key);
                $key = str_replace("-", "_", $key);

                $headers[$key] = $value;
            }

            return $headers;
        }

        if(is_array($_SERVER)){
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }

            return $headers;
        }

        return false;
    }

    public static function parseFromGlobals(){

        //Parse the URI relative to SCRIPT. This is done because you could be on virtual hosting and we only want
        //The URI relative to the index.php script (the front controller)
        $path = "";
        $uri = explode("/", rtrim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), "/")); //Remove trailing slash
        $script = explode("/", $_SERVER["SCRIPT_NAME"]);

        foreach($script as $position => $fragment){
            if(isset($uri[$position]) && (strtolower($uri[$position]) == strtolower($fragment))){
                $path .= $fragment;
                unset($uri[$position]);
                unset($script[$position]);
            }
        }

        //Disallow empty URI fragments such as: /user//profile. In other words, two backslashes cannot follow each other
        foreach($uri as $fragment){
            if(empty($fragment))
                throw new HTTPException_400;
        }

        $request = new Request();
        $request->setPath("/" . $path);
        $request->setUri("/" . implode("/", $uri));
        $request->setScript("/" . implode("/", $script));
        $request->setMethod(strtoupper($_SERVER["REQUEST_METHOD"]));
        $request->setDomain($_SERVER["HTTP_HOST"]);
        $request->setQuery($_GET);
        $request->setProtocol($_SERVER['SERVER_PROTOCOL']); //TODO change to a more reliable method for determining HTTPs

        //Get the IP - Guess at best
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
            $request->setIp($_SERVER['HTTP_CLIENT_IP']);

        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
            $request->setIp($_SERVER['HTTP_X_FORWARDED_FOR']);

        else
            $request->setIp($_SERVER['REMOTE_ADDR']);


        //Set headers.
        $headers = Request::getGlobalHeaders();
        $request->setHeaders($headers);

        //The request body is set to POST
        $request->setBody($_POST);

        //But we override it if content-type is application/json by reading the raw 'php://input'.  $HTTP_RAW_POST_DATA is deprecated and that's why we do this!
        if(($request->getMethod() == "POST" || $request->getMethod() == "PUT") && isset($headers["Content_Type"]) && (strpos($headers["Content_Type"], "application/json") !== false)){
            $request->setBody(json_decode(file_get_contents('php://input'), true));
        }

        return $request;
    }

}