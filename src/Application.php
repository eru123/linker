<?php

namespace Linker;

class Application {

    public ?\Linker\Router $router = NULL;
    public ?\Linker\PDO $pdo = NULL;
    public \Linker\Request $request; 

    private bool $use_env = false;
    private bool $use_config = false;
    private bool $use_router = false;
    private bool $use_pdo = false;
    private bool $use_upload = false;
    private bool $use_download = false;
    private bool $use_parser = false;
    private string $rootdir = './';
    public ?object $config = NULL;

    public function __construct(?string $rootdir = NULL){
        $this->rootdir = $rootdir ?? $this->rootdir;
        $this->request = new \Linker\Request;
    }
    public function loadEnv(): ?object {
        if(!$this->use_config && !$this->use_env){
            try {
                $dotenv = \Dotenv\Dotenv::createImmutable($this->rootdir);
                $dotenv->load();
                $this->use_env = true;
                $this->config = (object) $_ENV;
            } catch (\Exception $E){
                throw new \Exception("Invalid ENV File");
            }
        }
        return $this->config;
    }
    public function loadConfig($config = 'config.php') : ?object {
        if(!$this->use_env && !$this->use_config){
            if(is_string($config) && file_exists($config)){
                $file = include_once($config);
                $this->config = (object) $file;
            } elseif (is_array($config)){
                $this->config = (object) $config;
            } else throw new \Exception("Invalid Config");
            $this->use_config = TRUE;
        }
        return $this->config;
    }
    public function loadAutoConfig(){
        $possible_config_files = [
            "config.php",
            ".config.php",
            "cfg.php",
            ".cfg.php",
            "cnfg.php",
            ".cnfg.php",
            "confg.php",
            ".confg.php",
            "env.php",
            ".env.php"
        ];
        if(!$this->use_env && !$this->use_config && !$this->config){
            try {
                $this->loadEnv();
            } catch (\Exception $E){
                try {
                    $found = FALSE;
                    foreach($possible_config_files as $config){
                        if(file_exists( rtrim($this->rootdir,'/').'/'.ltrim($config,'/')) && !$found){
                            $found = TRUE;
                            $this->loadConfig($config);
                        }
                    }
                    if(!$found){
                        throw new \Exception("Invalid config file. ");
                    }
                } catch(\Exception $E) {
                    throw new \Exception("Both ENV file and config is invalid!");
                }
            }
        }
    }
    public function useRouter(){
        if(!$this->use_router){
            $this->loadAutoConfig();
            $this->router = new \Linker\Router($this);
            $this->use_router = true;
        }
        return $this->router;
    }
    public function usePDO(){
        if(!$this->use_pdo){
            $this->loadAutoConfig();
            $this->pdo = new \Linker\PDO($this);
            $this->use_pdo = TRUE;
        }
        return $this->pdo;
    }
    public function include(string $file,bool $duplicate = false){
        ($duplicate ? include($file) : include_once($file));
    }
}