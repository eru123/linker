<?php 

namespace Linker\Request;

class Query {

    public static function get(string $method = "request", string $str, $callback = null)
    {
        $keys = explode(" ", trim($str));
        $fkey = [];
        foreach ($keys as $key) {
            if (strlen(trim($key)) > 0) {
                if (count(explode(":", $key)) == 2) {
                    $lkey = explode(":", $key);
                    $fkey[$lkey[0]] = urldecode($lkey[1]);
                } else {
                    $fkey[$key] = "";
                }
            }
        }

        $params = self::rmatch($fkey, $method) ? self::translate($fkey, $method) : false;

        if (gettype($callback) == "object" && $params !== false) {
            // try {
            return $callback($params) ?? $params;
            // } catch (Exception $e) {
            // var_dump($e->getMessage());
            // throw new Exception("Query: Callback is invalid! ");
            // }

            return $params;
        } elseif ($params !== false) {
            return $params;
        }

        return false;
    }
    private static function _request($method, array $default = [])
    {
        $method = trim(strtolower((string) $method));

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
            case 'files':
                $REQ = $_FILES ?? $REQ;
                break;
            case 'server':
                $REQ = $_SERVER ?? $REQ;
                break;
            case 'env':
                $REQ = $_ENV ?? $REQ;
                break;
            default:
                $REQ = $_REQUEST ?? $REQ;
                break;
        }
        return $REQ;
    }
    private static function rmatch($arr, $method): bool
    {

        $REQ = self::_request($method, []);

        foreach ($arr as $k => $v) {
            if (strtolower($v) == "--r" || strtolower($v) == "-r" || strtolower($v) == "~r" || strtolower($v) == "!r") {
                if (isset($REQ[$k])) {
                    if (gettype($REQ[$k]) == "string" && strlen($REQ[$k]) <= 0) {
                        return false;
                    }
                } else {
                    return false;
                }
            } elseif (strlen(trim($v)) > 0) {
                if (isset($REQ[$k])) {
                    if ($REQ[$k] != $v) {
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
        return true;
    }
    private static function translate(array $arr, string $method): array
    {
        $REQ = self::_request($method, []);
        $res = [];
        foreach ($arr as $k => $v) {
            if (!empty($REQ[$k]) && isset($REQ[$k])) {
                $res[$k] = $REQ[$k];
            } else {
                $res[$k] = null;
            }
        }
        return $res;
    }
}