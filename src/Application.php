<?php

namespace Linker;

class Application {
    final public function __construct(array $paths){

        $app = $linker = $this;

        // Classes
        $class = include_once(rtrim($paths["core"],"/")."/classes.php");

        // Config
        $config = [];
        foreach($class["FS"]::scandirTree($paths["config"]) as $config_file)
            $config[pathinfo($config_file)["filename"]] = include_once($config_file);

        // PDO
        if(isset($config["pdo"]) && @$config["pdo"]["use"] === TRUE)
            $this->pdo = new $class["PDO"]($config["pdo"]);

        // PDO Model
        if(isset($this->pdo) && @$config["pdo"]["model"] === TRUE && count($config["pdo"]["models"] ?? []) > 0)
            foreach($config["pdo"]["models"] as $model)
                $this->model[$model] = new $class["PDOMODEL"]($model,$this->pdo);
        if(count($this->model ?? []) > 0) $this->model = (object) $this->model;

        // Upload 
        if(isset($config["uploader"]) && @$config["uploader"]["upload"] === TRUE)
            $this->upload = new $class["UPLOAD"]($config["uploader"]);
        
        // Download 
        if(isset($config["uploader"]) && @$config["uploader"]["download"] === TRUE)
            $this->download = new $class["DOWNLOAD"]();

        // Frecbase Keyval
        if(isset($config["frecbase"]) && @$config["frecbase"]["keyval"] === TRUE)
            $this->kv = new $class["KV"]();
        
        // Request Query
        if(isset($config["request"]) && @$config["request"]["query"] === TRUE)
            $this->query = new $class["QUERY"]();
        
        // Request URI
        if(isset($config["request"]) && @$config["request"]["uri"] === TRUE)
            $this->uri = new $class["URI"]();

        // Router
        if(isset($config["router"]) && @$config["router"]["use"] === TRUE){
            $route = (new $class["ROUTER"]($config["router"]))->result();
            $this->route = (object) $route;
            if(@$config["router"]["render"] == TRUE ){
                $router_dir = rtrim($config["router"]["dir"],"/")."/";
                if(is_file((string) $route["path"])){
                    $mime = $class["FS"]::mime_content_type($route["path"]);
                    if(pathinfo((string) $route["path"], PATHINFO_EXTENSION) == "js") $mime = "application/javascript";
                    if(
                        $mime !== FALSE && 
                        is_string($mime) &&
                        !preg_match('/text/i',$mime)
                    ) header("Content-Type: $mime");
                    include_once $route["path"];
                } elseif(
                    isset($config["router"]["error"]) && 
                    is_string($config["router"]["error"]) && 
                    is_file($router_dir.$config["router"]["error"])
                ){  
                    $mime = $class["FS"]::mime_content_type($router_dir.$config["router"]["error"]);
                    if(
                        $mime !== FALSE && 
                        is_string($mime) &&
                        !preg_match('/text/i',$mime)
                    ) header("Content-Type: $mime");
                    include_once $router_dir.$config["router"]["error"];
                } else die("PAGE NOT FOUND!");
            }
        }
    }
}