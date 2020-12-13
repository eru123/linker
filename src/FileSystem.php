<?php 

namespace Linker;

class FileSystem {
    final public static function mkdir($dir,$m=0700) {
		if (is_array($dir)) {
			foreach ($dir as $k => $v) $dir[$k] = self::mkdir($v);
			return $dir;
		} else {
			if (is_dir($dir))	return FALSE;
			if (mkdir($dir,$m))	return TRUE;
		}
	}
	final public static function scandir($dir){
		$path = rtrim($dir,'/').'/';
		if(!is_dir($dir)) return array();
		$dir = scandir($dir);
		$res = array();
		$c = 0;
		for ($i=2; $i < (count($dir)); $i++) {
			$res[$c] = $path.$dir[$i];
			$c++;
		}
		return $res;
	}
	final public static function scandirTree(string $dir){
		$base = self::scandir($dir);
		$tmp = $base;
		foreach ($base as $v) {
			if (is_dir($v)) {
				$a = self::scandirTree($v);
				foreach ($a as $vc) {
					$tmp[] = $vc;
				}
			}
		}
		return $tmp;
    }
    final public static function tree(string $dir){
		$base = self::scandir($dir);
		$tmp = $base;
		foreach ($base as $k => $v) {
			if (is_dir($v)) {
                $tmp[$k] = [
                    "folder" => $v,
                    "childs" => self::tree($v)
                ];
			}
		}
		return $tmp;
    }
    final public static function index(string $dir){
		$base = self::scandir($dir);
		$tmp = $base;
		foreach ($base as $k => $v) {
			if (is_dir($v)) {
                $tmp[$k] = [
                    "type" => "folder",
                    "path" => $v,
                    "name" => basename($v),
                    "childs" => self::index($v)
                ];
			} elseif (is_file($v)){
                $tmp[$k] = [
                    "type" => "file",
                    "path" => $v,
                    "name" => pathinfo(basename($v),PATHINFO_FILENAME ),
                    "ext" => pathinfo(basename($v),PATHINFO_EXTENSION),
                    "size" => filesize($v)
                ];
            }
		}
		return $tmp;
    }
	final public static function del($p){
		if (is_array($p) && count($p) > 0) {
			foreach ($p as $key => $value) $p[$key] = self::del($value);
			return $p;
		} else {
			if (is_dir($p)) {
				$dir = self::scandir($p);
				foreach ($dir as $key => $value) self::del($value);
				if (rmdir($p)) return TRUE;
			} elseif (file_exists($p)) {
				if (unlink($p)) return TRUE;
			}
		}
		return FALSE;
	}
	final public static function write(string $f,string $data='',string $m='a') : bool {
        $m = trim(strtolower($m));
		if ($m == 'a') {
			if (file_exists($f)) {
	    		$handle =  fopen($f, "a" ) ;
		        $res = fwrite($handle,$data);
		        fclose ($handle);
		        return $res;
	    	} else return self::write($f,$data,'w');
	    } elseif ($m == 'w') {
            if(!file_exists($f)) touch($f);
	        $handle =  fopen($f, "w" ) ;
	        $res = fwrite($handle,$data);
		    fclose ($handle);
		    return $res;
	    } else {
            return self::write($f,$data,'a');
        }
    }
    final public static function fwrite(string $f,string $data='') : bool {
        return self::write($f,$data,'w');
    }
    final public static function fappend(string $f,string $data='') : bool {
        return self::write($f,$data,'a');
	}
}