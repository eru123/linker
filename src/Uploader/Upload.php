<?php

namespace Linker\Uploader;

class Upload {
    private string $dir = "uploads";
    private string $exts = "";
    private float $max_size = 2.0; // Mega Bytes
    private array $errors = [];
    private bool $ready = false;
    public function __construct(array $config = []){
        $this->dir = $config["dir"] ?? $this->dir;
        $this->exts = $config["exts"] ?? $this->exts;
        $this->max_size = $config["max_size"] ?? $this->max_size;

        $this->dir = rtrim($this->dir,"/")."/";
    }
    private function is_ext_allowed(string $filename) : bool {
        $this->exts = trim($this->exts);
        $ext = strtolower(pathinfo($filename,PATHINFO_EXTENSION));
        foreach(explode(" ",$this->exts) as $val){
            if($val == $ext){
                return TRUE;
            }
        }

        $this->errors[] = "$filename - Invalid file extension (Allowed: ".implode(", ",explode(" ",$this->exts)).")";
        return FALSE;
    }
    private function is_size_allowed(int $size,string $filename): bool {
        if($this->max_size == 0 || $size/1024/1204 <= $this->max_size){
            return TRUE;
        }
        $this->errors[] = "$filename - Invalid file size (Allowed: $this->max_size MB)";
        return FALSE;
    }
    private function is_upload_error(string $key){
        if(isset($_FILES[$key])){
            switch ($_FILES[$key]["error"]) {
                case UPLOAD_ERR_OK:
                    return TRUE;
                    break;
                case UPLOAD_ERR_INI_SIZE:
                    $this->errors[] = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->errors[] = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->errors[] = "No file was uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->errors[] = "Missing a temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $this->errors[] = "Failed to write file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $this->errors[] = "A PHP extension stopped the file upload";
                    break;
                default:
                    break;
            }
        }

        return FALSE;
    }
    private function is_uploads_error(string $key){
        if(isset($_FILES[$key])){
            foreach($_FILES[$key]["name"] as $k => $fname){
                $fname = basename($fname);
                $fname = htmlentities($fname);
                switch ($_FILES[$key]["error"][$k]) {
                    case UPLOAD_ERR_OK:
                        break;
                    case UPLOAD_ERR_INI_SIZE:
                        $this->errors[] = "$fname exceeds the upload_max_filesize directive in php.ini";
                        break;
                    case UPLOAD_ERR_PARTIAL:
                        $this->errors[] = "$fname exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                        break;
                    case UPLOAD_ERR_NO_FILE:
                        $this->errors[] = "$fname was not uploaded";
                        break;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        $this->errors[] = "$fname is missing a temporary folder.";
                        break;
                    case UPLOAD_ERR_CANT_WRITE:
                        $this->errors[] = "$fname failed to write to disk.";
                        break;
                    case UPLOAD_ERR_EXTENSION:
                        $this->errors[] = "$fname - A PHP extension stopped the file upload";
                        break;
                    default:
                        break;
                }
            }
            
        }

        return FALSE;
    }
    public function file(string $key,?string $name = NULL){
        $ok = [];
        if(isset($_FILES[$key]) && !is_array($_FILES[$key]["name"])){
            $fname = basename($_FILES[$key]["name"]);
            $ext = strtolower(pathinfo($fname,PATHINFO_EXTENSION));
            $size = $_FILES[$key]["size"];

            $this->is_ext_allowed($fname);
            $this->is_size_allowed($size,$fname);
            $this->is_upload_error($key);
            
            $filename=$name!==NULL?$name.($ext==""?$ext:".$ext"):$fname;
            $path = $this->dir.$filename;
            
            if(file_exists($path)){
                $this->errors[] = "$filename already exists";
            }

            $ok[$filename]["name"] = $_FILES[$key]["name"];
            $ok[$filename]["size"] = $size;
            $ok[$filename]["mime"] = $_FILES[$key]["type"];

            $ok[$filename]["path"] = count($this->errors) == 0 && move_uploaded_file($_FILES[$key]["tmp_name"], $path) ? $path : FALSE;
            $ok[$filename]["errors"] = $this->errors;
        } else {
            return FALSE;
        }
        
        return $ok;
    }
    public function files(string $key,?object $name = NULL){
        $ok = [];
        $err = [];
        if(isset($_FILES[$key]) && is_array($_FILES[$key]["name"])){
            foreach($_FILES[$key]["name"] as $k => $v){

                $fname = basename($_FILES[$key]["name"][$k]);
                $ext = strtolower(pathinfo($fname,PATHINFO_EXTENSION));
                $size = $_FILES[$key]["size"][$k];

                $this->is_ext_allowed($fname);
                $this->is_size_allowed($size,$fname);
                $this->is_uploads_error($key);

                try {
                    $filename=gettype($name) == "object"?$name().($ext==""?$ext:".$ext"):$fname;
                } catch(\Exception $e){
                    $filename = $fname;
                }

                $path = $this->dir.$filename;
                
                if(file_exists($path)){
                    $this->errors[] = "$filename already exists";
                }
                
                $ok[$fname]["name"] = $_FILES[$key]["name"][$k];
                $ok[$fname]["size"] = $size;
                $ok[$fname]["mime"] = $_FILES[$key]["type"][$k];

                $ok[$fname]["path"] = count($this->errors) == 0 && move_uploaded_file($_FILES[$key]["tmp_name"][$k], $path) ? $path : FALSE;
                $ok[$fname]["errors"] = $this->errors;

                $err = array_merge($err, $this->errors);
                $this->errors = [];
            }
        } else {
            return FALSE;
        }
        
        return $ok;
    }
}