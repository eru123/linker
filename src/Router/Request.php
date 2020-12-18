<?php 


namespace Linker\Router;

class Request {
    // Get clean URI request
    // Filter URI and removed get request from URI
    public static function getPath() : string {
        $path = $_SERVER["REQUEST_URI"] ?? "/";
        $queryPosition = strpos($path,"?");
        $path = $queryPosition ? substr($path,0,$queryPosition) : $path;
        return "/".ltrim($path,"/");
    }
    // Convert 'getPath' result into an array with custom range 
    public static function getArrayPath(int $start = 0,?int $end = null) : array {
        $uri = self::getPath();
        $uri = ltrim($uri, "/");
        $pieces = explode("/",$uri);
        $end = $end === NULL && $end <= count($pieces) ? count($pieces) : $end;
        $end = $end - $start;
        return array_slice($pieces,$start,$end);
    }
}