<?php

namespace Linker\Router;

class Auto {

    public \Linker\Application $app;
    public string $basedir = "public";
    public string $index = "index readme README main home";
    public string $ext = ".php .html .htm .md .txt .mp4 .mp3"; // separated by spaces
    public string $mode = "history"; // history|query
    public string $error = ""; // case-sensitive (nospaces) format -> "/error.php"
    public int $level = 0;
    public bool $duplicate = false;

    public string $real_path;

    private string $default_basedir;
    private string $default_index;
    private string $default_mode; 

    public function __construct(\Linker\Application $app){
        $this->app = $app;
        $this->default_basedir = $this->basedir;
        $this->default_index = $this->index;
        $this->default_mode = $this->mode;
    }
    public function path() : string {
        $path = $this->mode == "query" 
        ? $this->app->request->getQueryPath() 
        : $this->app->request->getPath();
        
        return "/".trim($path,"/");
    }
    public function basedir() : string {
        $this->basedir = rtrim($this->basedir,"/");
        return $this->basedir;
    }
    public function mode() : string {
        $this->mode = trim(strtolower($this->mode));
        if(!$this->mode){
            $this->mode = $this->default_mode;
        }
        return $this->mode;
    }
    public function realPath() : string {
        
        $path = $this->path();
        
        if($this->mode() === "history"){
            $uri_pieces = explode("/",$path);
            $uri_pcscnt = count($uri_pieces);

            $this->level = $this->level >= $uri_pcscnt ? $uri_pcscnt - 1: $this->level;
            $this->level = $this->level < 0 ? $this->default_level : $this->level;
            
            // return $this->level;
            $uri_pcsnew = [];

            for ($i=$this->level + 1; $i < $uri_pcscnt; $i++) { 
                array_push($uri_pcsnew, $uri_pieces[$i]);
            }

            $path = "/".trim(implode("/",$uri_pcsnew),"/");
        }
        
        $this->real_path = $path;
        $this->real_path = str_replace("/../", "/",$this->real_path);

        return $this->real_path;
    }
    public function filematches(){

        $base = $this->basedir();
        $key = $this->realPath();
        $index = $this->index;
        $exts = explode(" ",$this->ext);
        $indexes = explode(" ",$index);
        $isKeyExt = FALSE;
        $isKeyDir = is_dir($base.$key);
        $isIndexExt = FALSE;
        $match_filekeys = [];
        $match_dirkeys = [];

        foreach($exts as $ext){
            $regex = '/'.$ext.'$/';
            
            $isKeyExt = preg_match($regex,$key) ? TRUE : $isKeyExt;
            $isIndexExt = preg_match($regex,$index) ? TRUE : $isIndexExt;
            if ($isKeyExt && !in_array($base.$key,$match_filekeys) && file_exists($base.$key)) {
                $match_filekeys[] = $base.$key;
            } elseif(!$isKeyExt && file_exists("$base$key$ext")) {
                $match_filekeys[] = "$base$key$ext";
            }
            if($isKeyDir){
                if(count($indexes) > 1){
                    foreach($indexes as $ndx){
                        if(file_exists("$base$key/$ndx$ext")){
                            $match_dirkeys[] = "$base$key/$ndx$ext";
                        }
                    }
                } elseif(file_exists("$base$key/$index$ext")){
                    $match_dirkeys[] = "$base$key/$index$ext";
                }
            }
        }
        return array_merge($match_filekeys,$match_dirkeys);
    }
    public function error(){
        $this->error = "/".trim($this->error,"/");
        return $this->error;
    }
    public function init(bool $return = false, bool $return404 = true) {
        $files = (array) $this->filematches();
        $file = "";
        $found = false;
        if(count($files) > 0){
            foreach($files as $filetest){
                if(!$found && file_exists($filetest)){
                    $file = $filetest;
                    $found = true;
                }
            }
        } elseif (!is_dir($this->basedir.$this->error()) && file_exists($this->basedir.$this->error)){
            $file = $this->basedir.$this->error;
        }
        if(file_exists($file)){
            $linker = $this->app;
            if($return){
                return $file;
            } else {
                ($this->duplicate ? include($file) : include_once($file));
                return $file;
            }
        } elseif($return404) {
            header('HTTP/1.0 404'); 
            // throw new \Exception("Linker Framework: Router::Auto() INVALID_URI or FILE_NOT_FOUND. URI=\"$this->real_path\". PATH=\"$file\"");
        } else {
            return NULL;
        }
    }
}