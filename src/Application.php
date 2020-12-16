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




        // Router
        if(isset($config["router"]) && @$config["router"]["use"] === TRUE){
            $route = (new $class["ROUTER"]($config["router"]))->result();
            $this->route = (object) $route;
            if(@$config["router"]["render"] == TRUE && is_file((string) $route["path"]))
                include_once $route["path"];
        }
    }
}