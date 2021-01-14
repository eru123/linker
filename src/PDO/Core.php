<?php

namespace Linker\PDO;

use \PDO;

class Core
{
    protected $pdo = null;
    protected $tb = null;
    protected $schema = null;

    public function __construct($config = null)
    {
        if (is_array($config)) {
            $this->connectByConfig($config);
        } elseif (is_object($config)) {
            $this->connectByApp($config);
        }

        if (isset($config["schema"]) && is_array($config["schema"]) && count($config["schema"])) {
            $schema = isset($config["schema_method"]) ? $config["schema_method"] : $schema = "dynamic";
            switch ($schema) {
                case 'normal':
                    $this->setupSchema($config["schema"]);
                    break;
                case 'force':
                    $this->forceSetupSchema($config["schema"]);
                    break;
                default:
                    $this->alteredSchema($config["schema"]);
                    break;
            }
        }
    }
    public function connect(string $user, string $pass, string $host, string $db): PDO
    {
        $dsn = "mysql:host=$host;dbname=$db";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->pdo = $pdo;
        return $pdo;
    }
    protected function connectByApp(object $config)
    {
        $user = $config->DB_USER ?? "";
        $pass = $config->DB_PASS ?? "";
        $host = $config->DB_HOST ?? "";
        $db = $config->DB_NAME ?? "";
        return $this->connect($user, $pass, $host, $db);
    }
    protected function connectByConfig(array $config)
    {
        $user = $config["user"] ?? "";
        $pass = $config["pass"] ?? "";
        $host = $config["host"] ?? "";
        $db = $config["db"] ?? "";
        return $this->connect($user, $pass, $host, $db);
    }
    public function columns(string $table)
    {
        $columns = [];
        try {
            $rs = $this->pdo->query("SELECT * FROM $table LIMIT 0");
            for ($i = 0; $i < $rs->columnCount(); $i++) {
                $col = $rs->getColumnMeta($i);
                $columns[] = $col['name'];
            }
        } catch (Exception $e) {
            //
        } catch (Error $e) {
            //
        }
        return $columns;
    }
    public function tables()
    {
        try {
            $tableList = array();
            $result = $this->pdo->query("SHOW TABLES");
            while ($row = $result->fetch(PDO::FETCH_NUM)) {
                $tableList[] = $row[0];
            }
            return $tableList;
        } catch (PDOException $e) {
            return [];
        }
    }
    public function deleteAllTables()
    {
        $query = "";
        foreach ($this->tables() as $table) {
            $query .= "DROP TABLE IF EXISTS $table;";
        }
        if (!empty($query)) {
            return $this->pdo->exec($query) == 0 ? TRUE : FALSE;
        }
        return FALSE;
    }
    public function setupSchema(array $schema = []): bool
    {
        // SCHEMA - { table: [column,...]}
        $schema = $schema ?? $this->schema;
        $query = "";

        foreach ($schema as $table => $columns) {

            $cols = ""; // Columns - translated into SQL query

            foreach ($columns as $column) {
                if ($column === "id") {
                    // Primary key has a default max value of 11
                    $cols .= "id int(11) AUTO_INCREMENT PRIMARY KEY,";
                } else {
                    $cols .= "$column LONGTEXT NOT NULL,";
                }
            }

            $cols = rtrim($cols, ",");
            $query .= "CREATE TABLE IF NOT EXISTS $table($cols);";
        }

        ($this->pdo)->exec($query);
        return true;
    }
    public function forceSetupSchema(array $schema = []): bool
    {
        // SCHEMA - { table: [column,...]}

        $schema = $schema ?? $this->schema;
        $query = "";

        foreach ($schema as $table => $columns) {

            $primary_key = false; // DEFAULT - Automatically set to true if id column is exists;
            $cols = ""; // Columns - translated into SQL query

            foreach ($columns as $column) {
                if ($column === "id") {
                    // Primary key has a default max value of 11
                    $primary_key = true;
                    $cols .= "id int(11) AUTO_INCREMENT PRIMARY KEY,";
                } else {
                    $cols .= "$column LONGTEXT NOT NULL,";
                }
            }

            $cols = rtrim($cols, ",");
            $query .= "DROP TABLE IF EXISTS $table;";
            $query .= "CREATE TABLE $table($cols);";
        }

        ($this->pdo)->exec($query);
        return true;
    }
    public function alteredSchema(array $schema = [])
    {
        $u = [];
        $r = [];
        $query = "";

        foreach ($schema as $table => $columns) {
            if (!$this->is_table($table)) {
                $u[$table] = $columns;
            } else {
                $r[$table] = $columns;
            }
        }

        if (count($u) > 0) {
            $this->setupSchema($u);
        }

        if (count($r) > 0) {
            foreach ($r as $t => $c) {
                // delete - ALTER TABLE `module_column` DROP COLUMN `module_id`
                // add - ALTER TABLE emails ADD <column name> varchar(60)
                $cs = $this->columns($t);
                $primary_key = false; // DEFAULT - Automatically set to true if id column is exists;
                $add = "";
                $drop = "";
                foreach ($c as $cl) {
                    if (!in_array($cl, $cs)) {
                        if ($cl === "id") {
                            $primary_key = true;
                            $add .= "ADD id int(11) AUTO_INCREMENT PRIMARY KEY,";
                        } else {
                            $add .= "ADD $cl LONGTEXT NOT NULL,";
                        }
                    }
                }

                foreach ($cs as $c1) {
                    if (!in_array($c1, $c)) {
                        $drop .= "DROP COLUMN $c1,";
                    }
                }

                $add = rtrim($add, ",") . ",";
                $drop = rtrim($drop, ",");
                $data = rtrim("$add$drop", ",");
                if (strlen($data) > 0) {
                    $query .= "ALTER TABLE $t $data;";
                }
            }
            if (strlen($query) > 0) {
                ($this->pdo)->exec($query);
            }
        }
        return true;
    }
    public function is_table(string $table)
    {
        try {
            $this->pdo->query("SELECT 1 FROM $table");
        } catch (PDOException $e) {
            return false;
        }
        return true;
    }
    public function table(string $table): void
    {
        $this->tb = $table;
    }
    public function createData(array $data): bool
    {
        // DATA - {key: value}
        $tb = $this->tb;
        $keys = "";
        $pdo_values = "";
        $values = [];

        foreach ($data as $key => $value) {
            $keys .= $key . ",";
            $pdo_values .= "?,";
            $values[] = $value;
        }

        $keys = rtrim($keys, ",");
        $pdo_values = rtrim($pdo_values, ",");

        $query = "INSERT INTO $tb($keys)VALUE($pdo_values)";

        $q = ($this->pdo)->prepare($query);
        $q->execute($values);
        if ($q->rowCount() > 0) {
            return true;
        }
        return false;
    }
    public function createUniqueData($key, array $data): bool
    {
        if (is_string($key)) {
            if (isset($data[$key]) && count($this->readData([$key => $data[$key]])) > 0) {
                return false;
            }
        }

        return $this->createData($data);
    }
    public function readData(array $find, array $advance = []): array
    {

        // find - ["name" => "jericho"]
        $tb = $this->tb;

        $prep_bind = $order = $limit = $offset = "";
        $bind_data = [];

        foreach ($find as $key => $value) {
            $prep_bind .= "$key=?,";
            $bind_data[] = $value;
        }

        if (isset($advance["order"])) {
            $order = " ORDER BY $advance[order]";
        }
        // Advance - ["order" => "id ASC|DESC"]
        if (isset($advance["limit"])) {
            $limit = " LIMIT $advance[limit]";
        }
        // Advance - ["limit" => 3]
        if (isset($advance["offset"])) {
            $offset = " OFFSET $advance[offset]";
        }
        // Advance - ["offset" => 1]

        $bind = (strlen($prep_bind) > 0) ? " WHERE " . rtrim($prep_bind, ",") : "";
        $query = "SELECT * FROM $tb$bind$order$limit$offset";

        $q = ($this->pdo)->prepare($query);
        $q->execute($bind_data);

        $result = $q->fetchAll() ?? [];

        if (isset($advance["columns"])) {
            $cols = $advance["columns"];
            if (is_string($cols)) {
                $cols = explode(",", $cols);
                foreach ($cols as $k => $v) {
                    $cols[$k] = trim($v);
                }
            } else if (is_array($cols)) {
                foreach ($cols as $k => $v) {
                    if (!is_string($v)) {
                        unset($cols[$k]);
                    }
                }
            } else {
                return $result;
            }
            return self::columnizer($result, $cols);
        }
        return $result;
    }
    public function readAllData(array $advance = []): array
    {
        $tb = $this->tb;

        $order = $limit = $offset = "";

        if (isset($advance["order"])) {
            $order = " ORDER BY $advance[order]";
        }
        // Advance - ["order" => "id ASC|DESC"]
        if (isset($advance["limit"])) {
            $limit = " LIMIT $advance[limit]";
        }
        // Advance - ["limit" => 3]
        if (isset($advance["offset"])) {
            $offset = " OFFSET $advance[offset]";
        }
        // Advance - ["offset" => 1]

        $query = "SELECT * FROM $tb$order$limit$offset";

        $q = ($this->pdo)->prepare($query);
        $q->execute();

        $result = $q->fetchAll() ?? [];

        if (isset($advance["columns"])) {
            $cols = $advance["columns"];
            if (is_string($cols)) {
                $cols = explode(",", $cols);
                foreach ($cols as $k => $v) {
                    $cols[$k] = trim($v);
                }
            } else if (is_array($cols)) {
                foreach ($cols as $k => $v) {
                    if (!is_string($v)) {
                        unset($cols[$k]);
                    }
                }
            } else {
                return $result;
            }
            return self::columnizer($result, $cols);
        }
        return $result;
    }
    private static function columnizer(array $a, array $columns): array
    {
        $rows = [];
        foreach ($a as $k => $r) {
            if (is_array($r)) {
                $cols = [];
                foreach ($columns as $col) {
                    if (is_string($col)) {
                        $cols[$col] = $r[$col] ?? NULL;
                    }
                }
                $rows[$k] = $cols;
            }
        }
        return $rows;
    }
    public function updateData(array $find, array $data): bool
    {
        // Find - [key => value]
        // Data = [key => value]
        $tb = $this->tb;

        $prep_data = $prep_find = "";
        $new_data_find = [];

        foreach ($data as $data_key => $data_val) {
            $prep_data .= "$data_key=?,";
            $new_data_find[] = $data_val;
        }

        foreach ($find as $find_key => $find_val) {
            $prep_find .= "$find_key=?,";
            $new_data_find[] = $find_val;
        }

        $prep_data = rtrim($prep_data, ",");
        $prep_find = rtrim($prep_find, ",");

        $query = "UPDATE $tb SET $prep_data WHERE $prep_find";

        $q = ($this->pdo)->prepare($query);
        $q->execute($new_data_find);

        if ($q->rowCount() > 0) {
            return true;
        }

        return false;
    }
    public function deleteData(array $find): bool
    {
        $prep_find = "";
        foreach ($find as $key => $value) {
            $prep_find .= "$key=?,";
            $new_find[] = $value;
        }

        $prep_find = rtrim($prep_find, ",");

        $query = "DELETE FROM $this->tb WHERE $prep_find";
        $q = ($this->pdo)->prepare($query);
        $q->execute($new_find);

        if ($q->rowCount() > 0) {
            return true;
        }

        return false;
    }
    public function deleteAllData(): bool
    {
        try {
            $sql = 'DELETE FROM $this->tb';
            $this->pdo->exec($sql);
            return TRUE;
        } catch (Throwable $e) {
            return FALSE;
        }
    }
}