<?php

namespace Linker\Cache;

class Memory {
    public $use = "apcu"; // apc || apcu
    public function __construct(?string $use = NULL){
        if(!$use){
            if($this->isAPCu()){
                $use = "apcu";
            } elseif($this->isAPC()) {
                $use = "apc";
            } else {
                $use = $this->use;
            }
        }
        $this->use = $use;
    }
    
    public function set(string $key, $value, int $expiration = 0){
        if($expiration <= 0) $expiration = 86400;
        if($this->use == "apcu" && function_exists("apcu_store")){
            return apcu_store($key,$value,$expiration);
        } elseif($this->use == "apc" && function_exists("apc_store")){
            return apc_store($key,$value,$expiration);
        }
        return FALSE;
    }
    public function get(string $key,$default = FALSE){
        if($this->use == "apcu" && function_exists("apcu_exists") && function_exists("apcu_fetch")){
            if(apcu_exists($key)){
                return apcu_fetch($key,$default);
            }
        } elseif($this->use == "apc" && function_exists("apc_exists") && function_exists("apc_fetch")){
            if(apc_exists($key)){
                return apc_fetch($key,$default);
            }
        }
        return FALSE;
    }
    public function delete(string $key){
        if($this->use == "apcu" && function_exists("apcu_delete ")){
            return apcu_delete($key);
        } elseif($this->use == "apc" && function_exists("apc_delete")){
            return apc_delete($key);
        }
        return FALSE;
    }
    public function isAPC(){
        if(function_exists('apc_enabled') && apc_enabled()){
            return TRUE;
        }
        return FALSE;
    }
    public function isAPCu(){
        if(function_exists('apcu_enabled') && apcu_enabled()){
            return TRUE;
        }
        return FALSE;
    }
}