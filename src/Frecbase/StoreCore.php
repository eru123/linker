<?php 

namespace Linker\Frecbase;

use \Linker\FileSystem\Core as FS;

class StoreCore {
    private $dir;
    public function __construct(?string $dir = NULL){
        $this->dir = $this->setDir($dir);
    }
    public function setDir(string $dir){
        $dir = rtrim($dir,"/");
        $this->dir = $dir."/";
        FS::fixDir($dir);
    }
    
    public function getDocPath(string $docName){
        return $this->dir.$docName.".php";
    }
    public function create(string $docName){
        FS::fwrite($this->getDocPath($docName),"<?php \$data=NULL;\n");
    }
    private static function json_encode($arr)
    {
        $encoded = json_encode($arr);
        $encoded = str_replace('\\\'', '\'', $encoded);
        $encoded = str_replace('\'', '\\\'', $encoded);
        return $encoded;
    }
    public function doCheck(string $doc){
        $f = $this->getDocPath($doc);
        if(!is_file($f)){
            $this->create($doc);
        }
        return $f;
    }
    public function set(string $docName,$val,$overwrite = FALSE){
        $f = $this->doCheck($docName);
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
        return $overwrite ? 
            FS::fwrite($f, "<?php\n\$data = $data;\n")  : 
            FS::fappend($f, "\$data = $data;\n");
    }
    public function get(string $docName,$default){
        $f = $this->doCheck($docName);
        $get = function (string $file, $default) {
            include $file;
            return isset($data) ? $data : $default;
        };
        return $get($f, $default);
    }
    public function delete(string $docName){
        $file = $this->getDocPath($docName);
        if(is_file($file)){
            return FS::del($file);
        }
        return FALSE;
    }
}