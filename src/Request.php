<?php 

namespace Linker;

class Request {
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
    public static function query(?string $method, string $str, $callback = null) : array {
        
        $keys = explode(" ", trim($str));
		$fkey = [];
		foreach ($keys as $key) {
			if (strlen(trim($key)) > 0) {
				if (count(explode(":",$key)) == 2) {
					$lkey = explode(":",$key);
					$fkey[$lkey[0]] = urldecode($lkey[1]);
				} else {
					$fkey[$key] = "";
				}
			}
		}

		$params = self::match($fkey, $method) ? self::translate($fkey,$method) : FALSE;

		if (gettype($callback) == "object" && $params !== FALSE) {
			try {
                return $callback($params) ?? ( $params ?? []);
            } catch(\Exception $e) {
                // Do Nothing
                // Keep
                // Calm
                // and
                // Love
                // Me
            }

            return $params ?? [];
        }

        return array();
    }
    private static function _request(?string $method, array $default = []){
        $method = trim(strtolower((string)$method));

        $REQ = $default;

        switch ($method) {
            case 'post':
                $REQ = $_POST ?? $REQ;
                break;
            case 'get':
                $REQ = $_GET ?? $REQ;
            case 'put':
                $REQ = $_PUT ?? $REQ;
                break;
            case 'delete':
                $REQ = $_DELETE ?? $REQ;
                break;
            case 'file':
                $REQ = $_FILE ?? $REQ;
                break;
            case 'files':
                $REQ = $_FILES ?? $REQ;
                break;
            case 'server':
                $REQ = $_SERVER ?? $REQ;
                break;
            default:
                $REQ = $_REQUEST ?? $REQ;
                break;
        }
        return $REQ;
    }
	private static function match(array $arr,?string $method) : bool {
        
        $REQ = self::_request($method,[]);
        
        foreach ($arr as $k => $v) {
			if (strtolower($v) == "--r" || strtolower($v) == "-r" || strtolower($v) == "~r" || strtolower($v) == "!r") {
				if(isset($REQ[$k])) {
					if (gettype($REQ[$k]) == "string" && strlen($REQ[$k]) <= 0) 
						return FALSE;
				} else return FALSE;
			} elseif (strlen(trim($v)) > 0) {
				if(isset($REQ[$k])) {
					if ($REQ[$k] != $v) 
						return FALSE;
				} else return FALSE;
			}
		}
		return TRUE;
	}
	private static function translate(array $arr,string $method) : array {
        $REQ = self::_request($method,[]);
		$res = [];
		foreach ($arr as $k => $v) {
			if (!empty($REQ[$k]) && isset($REQ[$k])) {
				$res[$k] = $REQ[$k];
			} else {
				$res[$k] = NULL;
			}
		}
		return $res;
    }
    private static function json(array $a,string $accessControlAllowOrigin = "*") : void {
		header('Access-Control-Allow-Origin: $accessControlAllowOrigin');
		header('Content-Type: application/json');
		echo json_encode($a);
	}
	public static function resolve(array $res, array $default = [],$cors = "*") : void {
		if (count($res) <= 0)
			$res = $default;
		self::json($res,$cors);
	}
}