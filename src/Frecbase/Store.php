<?php

namespace Linker\Frecbase;

use \Linker\Frecbase\StoreCore as Core;

class Store {
    public $dir;
    public $doc;
    public $data = NULL;
    private $core;
    public function __construct(?string $dir = NULL,?string $doc= NULL){
        $this->dir = $dir;
        $this->doc = $doc;
        $this->core = new Core($dir);
    }
    public function save(){
        $this->core->setDir($this->dir);
        return $this->core->set($this->doc,$this->data);
    }
    public function dir(string $dir){
        $this->dir = $dir;
        return $this;
    }
    public function directory(string $dir){
        return $this->dir($dir);
    }
    public function doc(string $doc){
        $this->doc = $doc;
        return $this;
    }
    public function document(string $doc){
        return $this->doc($doc);
    }
    public function getData($default = NULL){
        $this->core->setDir($this->dir);
        $this->data = $this->core->get($this->doc,$default);
        return $this;
    }
    public function optimize(){
        if($this->data == NULL){
            $this->getData();
        }
        $this->core->set($this->doc,$this->data,TRUE);
        return $this;
    }
    public function clear(){
        $this->core->setDir($this->dir);
        $this->core->set($this->doc,NULL,TRUE);
        return $this;
    }
}