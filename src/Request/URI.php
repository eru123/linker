<?php 

namespace Linker\Request;

class URI {
    public static function getPath(){
        $path = $_SERVER["REQUEST_URI"] ?? "/";
        $queryPosition = strpos($path,"?");
        $path = $queryPosition ? substr($path,0,$queryPosition) : $path;
        return "/".trim($path,"/");
    }
    public static function getQueryPath(){
        $path = $_SERVER["REQUEST_URI"] ?? "/";
        
        $queryPosition = strpos($path,"?") ? strpos($path,"?") + 1: FALSE;
        $path = $queryPosition ? substr($path,$queryPosition) : "/";
        
        $andPosition = strpos($path,"&");
        $path = $andPosition ? substr($path,0,$andPosition) : $path;

        return "/".trim($path,"/");
    }
}