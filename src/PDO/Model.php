<?php


namespace Linker\Database\PDO;

class Model {
    protected \Linker\Database\PDO $pdo;
    protected string $tb;

    public function __construct(string $table, \Linker\Database\PDO $pdo){
        $this->tb = $table;
        $this->pdo = $pdo;
    }
    private function table(){
        if($this->pdo->is_table($this->tb)){
            $this->pdo->table($this->tb);
        } throw new \Exception("Failed to access $this->tb table");
    }
    public function new(array $data){
        $this->table();
        return $this->pdo->createData($data);        
    }
    public function unique(string $column,array $data){
        $this->table();
        return $this->pdo->createUniqueData($column, $data);
    }
    public function column(string $column,array $find, array $advance = []){
        $result = $this->row($find,$advance);
        return isset($result[$column]) ? $result[$column] : NULL;
    }
    public function columns($column,array $find, array $advance = []){
        $columns = [];
        $fresult = [];

        if(is_string($column)){
            foreach(explode(',',$column) as $col){
                if(is_string($col) && trim($col) != ""){
                    $columns[] = trim($col);
                }
            }
        } elseif(is_array($column)){
            foreach($column as $col){
                if(is_string($col) && trim($col) != ""){
                    $columns[] = trim($col);
                }
            }
        }

        $result = $this->row($find,$advance);
        
        foreach($columns as $col){
            $fresult[$col] = $result[$col] ?? NULL;
        }

        return $fresult;
    }
    public function row(array $find,array $advance = []){
        $this->table();
        $advance["limit"] = 1;
        $advance["offset"] = 0;
        $result = $this->pdo->readData($find,$advance);
        return count($result) > 0 ? $result[0] : [];
    }
    public function rows(array $find,array $advance = []){
        $this->table();
        $result = $this->pdo->readData($find,$advance);
        return $result;
    }
    public function all(array $advance = []){
        $this->table();
        return $this->pdo->readAllData($advance);
    }
    public function update(array $find, array $data){
        $this->table();
        return $this->pdo->updateData($find,$data);
    }
    public function delete(array $find){
        $this->table();
        return $this->pdo->deleteData($find);
    }
}