<?php
/**
 * can be use only in the specified multidimensional array format.
 * array(
 *     array(
 *         "id" => 324,
 *         "key" => "value"
 *     ),
 *     array(
 *         "id" => 324,
 *         "key" => "value"
 *     )
 * )
 */
namespace Linker\Array;

class SearchRow {
    public static function like(string $regex,string $key,array $array,bool $newKey = FALSE) : array {
        $res = [];
        if($newKey === TRUE){
            foreach($array as $v){
                if(preg_match($regex,$v[$key])) {
                    $res[] = $v;
                }
            }
        } else {
            foreach($array as $k => $v){
                if(preg_match($regex,$v[$key])) {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }
    public static function exact($search,string $key,array $array,bool $newKey = FALSE){
        $res = [];
        if($newKey === TRUE){
            foreach($array as $v[$key]){
                if($search == $v) {
                    $res[] = $v;
                }
            }
        } else {
            foreach($array as $k => $v){
                if($search == $v[$key]) {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }
    public static function not($search,string $key,array $array,bool $newKey = FALSE){
        $res = [];
        if($newKey === TRUE){
            foreach($array as $v[$key]){
                if($search != $v) {
                    $res[] = $v;
                }
            }
        } else {
            foreach($array as $k => $v){
                if($search != $v[$key]) {
                    $res[$k] = $v;
                }
            }
        }
        return $res;
    }
    public static function multiLike(array $regex,string $key,array $array,bool $newKey = FALSE) : array {
        $res = [];
        if($newKey === TRUE){
            foreach($array as $v){
                foreach($regex as $reg){
                    if(preg_match($reg,$v[$key])) {
                        $res[] = $v;
                        break;
                    }
                }
            }
        } else {
            foreach($array as $k => $v){
                foreach($regex as $reg){
                    if(preg_match($reg,$v[$key])) {
                        $res[$k] = $v;
                        break;
                    }
                }
            }
        }
        return $res;
    }
    public static function multiExact(array $search,string $key,array $array,bool $newKey = FALSE){
        $res = [];
        if($newKey === TRUE){
            foreach($array as $v[$key]){
                foreach($search as $srch){
                    if($srch == $v) {
                        $res[] = $v;
                    }
                }
            }
        } else {
            foreach($array as $k => $v){
                foreach($search as $srch){
                    if($srch == $v) {
                        $res[$k] = $v;
                    }
                }
            }
        }
        return $res;
    }
    public static function multiNot(array $search,string $key,array $array,bool $newKey = FALSE){
        $res = [];
        if($newKey === TRUE){
            foreach($array as $v[$key]){
                foreach($search as $srch){
                    if($srch != $v) {
                        $res[] = $v;
                    }
                }
            }
        } else {
            foreach($array as $k => $v){
                foreach($search as $srch){
                    if($srch != $v) {
                        $res[$k] = $v;
                    }
                }
            }
        }
        return $res;
    }
}