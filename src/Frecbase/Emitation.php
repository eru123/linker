<?php 

namespace Linker\Frecbase;

use \Linker\FileSystem\Core as FS;
use \Exception;
class Emitation {

    protected $dir;
    protected $file;
    protected $db;
    protected $tb;
    protected $col_key;
    protected $row_key;

    public function __construct(?string $dir = NULL,?string $file = NULL){
        $this->dir = $dir;
        $this->file = $file;
        if($dir !== NULL){
            $this->set_dir($this->dir);
            if($this->file !== NULL){
                $this->set_file($this->file);
            }
        }
    }
    public function set_dir(string $dir){
        $dir = rtrim($dir,"/");
        $this->dir = $dir."/";
        FS::fixDir($dir);
        return $this;
    }
    public function set_file(string $file){
        $this->file = $file;
        $this->file_check();
        return $this;
    }
    private function file_path() : string {
        $file = $this->dir.$this->file.".php";
        return (string) $file;
    }
    private function file_check() : bool {
        $file = $this->file_path();
        if(!is_file($file)){
            return $this->file_init($file);
        }
        return TRUE;
    }
    private function file_init(){
        $file = $this->file_path();
        return (bool) FS::fwrite($file,"<?php \$data=[];\n");
    }
    private static function json_encode($arr)
    {
        $encoded = json_encode($arr);
        $encoded = str_replace('\\\'', '\'', $encoded);
        $encoded = str_replace('\'', '\\\'', $encoded);
        return $encoded;
    }
    private static function convert_data($val){
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
        return $data;
    }
    public function get_data($default = []){
        $file = $this->file_path();
        $get = function (string $file, $default) {
            include $file;
            return isset($data) && is_array($data) ? $data : $default;
        };
        return $get($file, $default);
    }
    public function select_database(string $db){
        $file = $this->file_path();
        $data = $this->get_data();
        if(is_array($data) && isset($data[$db])){
            $this->db = $db;
            return $this;
        } else {
            throw new Exception("Database \"$db\" is not exists.");
        }
    }
    public function create_database(string $db,bool $overwrite = FALSE){
        $file = $this->file_path();
        $db = str_replace("'","\'",$db);
        
        if($overwrite !== TRUE){
            $data = $this->get_data();
            if(is_array($data) && !isset($data[$db])){
                FS::fappend($file,"\$data['$db'] = [];\n") ?? 
                throw new Exception("Failed to create database \"$db\".");
            }
        } else {
            FS::fappend($file,"\$data['$db'] = [];\n") ?? 
            throw new Exception("Failed to create database \"$db\".");
        }
        return $this;
    }
    public function select_table(string $tb){
        $file = $this->file_path();
        $db = $this->db;
        $data = $this->get_data();
        if(is_array($data) && isset($data[$db]) && isset($data[$db][$tb])){
            $this->tb = $tb;
            return $this;
        } else {
            throw new Exception("Table \"$tb\" is not exists in database \"$db\".");
        }
    }
    public function create_table(string $tb,bool $overwrite = FALSE){
        $db = $this->db;
        $file = $this->file_path();
        
        if($overwrite !== TRUE){
            $data = $this->get_data();
            if(is_array($data) && isset($data[$db]) && !isset($data[$db][$tb])){
                FS::fappend($file,"\$data['$db']['$tb'] = [];\n") ?? 
                throw new Exception("Failed to create table \"$tb\" on database \"$db\".");
            }
        } else {
            FS::fappend($file,"\$data['$db']['$tb'] = [];\n") ?? 
            throw new Exception("Failed to create table \"$tb\" on database \"$db\".");
        }
        return $this;
    }
    public function select_id(string $id){
        $file = $this->file_path();
        $db = $this->db;
        $tb = $this->tb;
        $data = $this->get_data();
        if(is_array($data) && 
            isset($data[$db]) && 
            isset($data[$db][$tb]) && 
            isset($data[$db][$tb][$id])
        ){
            $this->row_key = $id;
            return $this;
        } else {
            throw new Exception("Row ID \"$id\" is not exists in the table \"$db\".");
        }
    }
    public function select_field(string $field){
        $file = $this->file_path();
        $db = $this->db;
        $tb = $this->tb;
        $id = $this->row_key;
        $data = $this->get_data();
        if(is_array($data) && 
            isset($data[$db]) && 
            isset($data[$db][$tb]) && 
            isset($data[$db][$tb][$id]) && 
            isset($data[$db][$tb][$id][$field])
        ){
            $this->col_key = $field;
            return $this;
        } else {
            throw new Exception("Field ID \"$field\" is not exists in selected row \"$db\".");
        }
    }
    public function create_data(string $id,array $udata,bool $overwrite = FALSE){
        $db = $this->db;
        $tb = $this->tb;
        $ndata = self::convert_data($udata);
        $file = $this->file_path();
        if($overwrite !== TRUE){
            $data = $this->get_data();
            if(is_array($data) && isset($data[$db]) && isset($data[$db][$tb]) && !isset($data[$db][$tb][$id])){
                FS::fappend($file,"\$data['$db']['$tb']['$id'] = $ndata;\n") ?? 
                throw new Exception("Failed to data in table \"$tb\" on database \"$db\".");
            }
        } else {
            FS::fappend($file,"\$data['$db']['$tb']['$id'] = $ndata;\n") ?? 
            throw new Exception("Failed to create data in table \"$tb\" on database \"$db\".");
        }
        return $this;
    }
}