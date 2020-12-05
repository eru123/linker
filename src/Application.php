<?php

namespace Linker;

class Application {
    public $pdo;
    public \Linker\Router $router;
    public \Linker\Request $request;

    public function __construct(){
        $this->request = new \Linker\Request;    
    }
    public function loadEnv(string $dir): void {
        $dotenv = \Dotenv\Dotenv::createImmutable($dir);
		$dotenv->load();
    }
    public function useRouter(){
        $this->router = new \Linker\Router($this);
        return $this->router;
    }

}