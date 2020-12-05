<?php

namespace Linker;


class Router {
    public \Linker\Application $app;
    public \Linker\Router\Auto $auto;
    public function __construct(\Linker\Application $app){
        $this->app = $app;
    }
    public function useAuto(){
        $this->auto = new \Linker\Router\Auto($this->app);
        return $this->auto;
    }
}