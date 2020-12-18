<?php 

namespace Linker\Frecbase;

use \Linker\FileSystem\FileSystem as FS;

class Keyval {
    private $file;
    public function __construct(?string $file = NULL){
        $this->file($file);
    }
    public function file(?string $file = NULL){
        if($file){
            $this->file = $file;
            $this->file_init();
        }
    }
    public function clear(){
        FS::fwrite($this->file,"<?php\n\$data = [];\n");
    }
    private function file_init(){
        if(!file_exists($this->file)){
            self::clear();
        }
    }
    private static function json_encode($arr){
        $encoded = json_encode($arr);
        $encoded = str_replace('\\\'','\'',$encoded);
        $encoded = str_replace('\'','\\\'',$encoded);
        return $encoded;
    }
    private static function filter_key(string $key){
        $key = str_replace('\\\'','\'',$key);
        $key = str_replace('\'','\\\'',$key);
        return $key;
    }
    public function set(string $key,$val){
        $key = self::filter_key($key);
        switch (gettype($val)) {
            case 'string':
                $data = "\"$val\"";
                break;
            case 'object':
                $nObj = self::json_encode($val);
                $data = "json_decode('$nObj')";
                break;
            case 'array':
                $nObj = self::json_encode($val);
                $data = "json_decode('$nObj',true)";
                break;
            case 'boolean':
                $data = $val ? "true" : "false";
                break;
            case 'NULL':
                $data = "NULL";
                break;
            case 'null':
                $data = "NULL";
                break;
            default:
                $data = $val;
                break;
        }
        FS::fappend($this->file,"\$data['$key'] = $data;\n");
    }
    public function get(string $key, $default = NULL){
        $get = function(string $file,string $key,$default){
            include($file);
            return isset($data) && isset($data[$key]) ? $data[$key] : $default;
        };
        return $get($this->file,$key,$default);
    }
    public function all($default = NULL){
        $get = function(string $file,$default){
            include($file);
            return isset($data) ? $data : $default;
        };
        return $get($this->file,$default);
    }
    public function del(string $key){
        $key = self::filter_key($key);
        FS::fappend($this->file,"unset(\$data['$key']);\n");
    }
    
}