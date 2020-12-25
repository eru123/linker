<?php 

namespace Linker\Router;

class Auto extends Request {
    private array $params = [];
    private array $index = [];
    private array $exts = [];
    private array $exclude = [];
    private string $dir = "";
    private string $path = "";
    private int $level = 0;
    public function __construct(array $config = []){
        $this->load_index($config);
        $this->load_exts($config);
        $this->load_dir($config);
        $this->load_level($config);
        $this->exclude = $config["exclude"] ?? $this->exclude;
    }
    // Fix indexes
    private function load_index(array $config) : void {
        $default_string_index = "index";
        $unsafe_string_index = trim($config["index"] ?? $default_string_index);
        $unsafe_index = explode(" ",$unsafe_string_index);
        $safe_index = [];

        foreach($unsafe_index as $sample)
            if(!preg_match("/[^A-Za-z0-9_\-%.]/",$sample))
                $safe_index[] = $sample;

        $this->index = $safe_index;
    }
    // Fix exts
    private function load_exts(array $config) : void {
        $default_string_exts = "php html";
        $string_exts = trim($config["exts"] ?? $default_string_exts);
        $unsafe_exts = explode(" ",$string_exts);
        $exts = [];
        foreach($unsafe_exts as $sample)
            if(!empty($sample))
                $exts[] = $sample;
        $this->exts = $exts;
    }
    // Fix base directory
    private function load_dir(array $config) : void {
        $dir = $config["dir"] ?? "public";
        if(!is_dir($dir)) throw new \Exception("Auto routing directory doesn't exists. ");
        $dir = rtrim($dir,"/");
        $this->dir = $dir;
    }
    // Fix uri leveling for sub directories
    private function load_level(array $config) : void {
        $level = $config["level"] ?? 0;
        $level = $level < 0 ? 0 : $level;
        $level = $level >= count(self::getArrayPath()) ? count(self::getArrayPath()) - 1: $level;
        $this->level = $level;
    }
    // Test all exts from given path 
    private function mock_exts(string $path) {
        foreach($this->exts as $ext){
            if(file_exists("$path.$ext")){
                return "$path.$ext";
            }
        }
        return FALSE;
    }
    // Retrieved all params in a directory
    private function params_list(string $dir) : array {
        $res = [];
        if(is_dir($dir)){
            foreach(scandir($dir) as $d){
                if(substr($d,0,1) == "_"){
                    $res[] = substr($d,1);
                }
            }
        }
        return $res;
    }
    // Test all indexes from given path
    private function mock_index_of(string $dir) {
        $dir = rtrim($dir,"/")."/";
        if(is_dir($dir)){
            foreach($this->index as $idx){
                $mext = $this->mock_exts($dir.$idx);
                if(is_string($mext) && file_exists($mext)){
                    return $mext;
                }
            }
        }
        return FALSE;
    }
    // Get the first param dir of the first element in params
    private function mock_params_of(string $dir) {
        $params = $this->params_list($dir);
        return (count($params) > 0 && is_dir($dir."/_".$params[0])) ? $params[0] : FALSE;
    }
    // Returns a string of the auto path result
    // Return false if no result
    private function mock() {
        $path = "";
        $uri_obj = self::getArrayPath();
        $levels = count($uri_obj);

        $amio_1 = $this->mock_index_of($this->dir);
        if($levels > 0){
            for ($i=$this->level; $i < $levels; $i++) { 
                if($uri_obj[$i] != ".." && $uri_obj[$i] != "." && substr($uri_obj[$i],0,1) != "_"){
                    if($i == $levels-1 && is_file($this->dir.$path."/".$uri_obj[$i])) {
                        $path = $this->dir.$path."/".$uri_obj[$i];
                    } elseif($i == $levels-1 && is_dir($this->dir.$path."/".$uri_obj[$i])){
                        $path .= "/".$uri_obj[$i];
                        $path = $this->mock_index_of($this->dir.$path);
                    } elseif(is_dir($this->dir.$path."/".$uri_obj[$i])){
                        $path .= "/".$uri_obj[$i];
                    } else {
                        $param = $this->mock_params_of($this->dir.$path);
                        $mock_ext = $this->mock_exts($this->dir.$path."/".$uri_obj[$i]);
                        if($i == $levels-1 && is_string($mock_ext) && file_exists($mock_ext)){
                            $path = $mock_ext;
                        } elseif(is_string($param)){
                            $this->params[preg_replace("/^_/","",$param)] = $uri_obj[$i];
                            $path .= "/_$param";
                            if($i == $levels-1 && is_dir($this->dir.$path)){
                                $path = $this->mock_index_of($this->dir.$path);
                            }
                        } else {
                            return FALSE;
                        }
                    }
                }
            }
            $this->path = $path;
            
            foreach($this->exclude as $regex){
                if(preg_match($regex,$this->path)){
                    $this->path = "";
                    return FALSE;
                }
            }

            return $path;
        }
        return FALSE;
    }
    public function path() {
        if(empty($this->path)){
            return $this->mock();
        }
        return $this->path;
    } 
    public function params() : array {
        if(count($this->params) == 0){
            $this->mock();
        }
        return $this->params;
    }
    public function result() : array {
        return [
            "uri" => self::getPath(),
            "path" => $this->path(),
            "params" => $this->params()
        ];
    }
}