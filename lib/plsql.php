<?php

    class PLSQLException {
        const DATABASE_EXISTS = "Database already exists!";
        const NO_DATABASE_FOUND = "There is no such database!";
        const TABLE_EXISTS = "Table already exists!";
        const NO_TABLE_FOUND = "There is no such table!";
        const CANNOT_OPEN_FILE = "Cannot open file!";
    }

?>


<?php

    class PLSQL {

        private $databases;

        public function __construct() {
            $this->databases = array();
            $this->createDatabasesFolder();
        }

        public function createDatabase($name) {
            if ($this->getDatabaseIndexByName($name) == -1) {
                $database = new Database($name);
                $this->databases[] = $database;
                $this->saveDatabase($database);
                return $database;
            } else {
                throw new Exception(PLSQLException::DATABASE_EXISTS);
            }
        }

        public function selectDatabase($name) {
            $index = $this->getDatabaseIndexByName($name);
            if ($index != -1) {
                return $this->databases[$index];
            } else {
                throw new Exception(PLSQLException::NO_DATABASE_FOUND);
            }
        }

        public function dropDatabase($name) {
            $index = $this->getDatabaseIndexByName($name);
            if ($index != -1) {
                array_splice($this->databases, $index, 1);  
                unlink($this->prepareFilePath($name));
                return true;
            } else {
                throw new Exception(PLSQLException::NO_DATABASE_FOUND);
            }
        }

        public function saveDatabase($database) {
            $content = json_encode($database);
            $file = fopen($this->prepareFilePath($database->getName()), "w") or die(PLSQLException::CANNOT_OPEN_FILE);
            fwrite($file, $content);
            fclose($file);
        }

        public function loadDatabase($path) {
            $file = fopen($path, "r") or die(PLSQLException::CANNOT_OPEN_FILE);
            $content = fread($file, filesize($path));
            $database = Database::parseArray((array)json_decode($content));
            $this->databases[] = $database;
            return $database;
        }

        private function createDatabasesFolder() {
            $path = "db";
            if(!file_exists($path)) {
                mkdir($path, 0777, true);
            }
        }

        private function prepareFilePath($name) {
            $path = "db";
            $file = $name.".db.json";
            return $path."/".$file;
        }

        private function getDatabaseIndexByName($name) {
            for ($i = 0; $i < count($this->databases); $i++) {
                $item = $this->databases[$i];
                if ($item->getName() == $name) {
                    return $i;
                }
            }
            return -1;
        }

    }

?>


<?php

    class Database implements JsonSerializable {

        private $name;
        private $tables;

        public function __construct($name) {
            $this->name = $name;
            $this->tables = array();
        }

        public function createTable($name, $scheme) {
            if ($this->getTableIndexByName($name) == -1) {
                $table = new Table($name, $scheme);
                $this->tables[] = $table;
                return $table;
            } else {
                throw new Exception(PLSQLException::TABLE_EXISTS);
            }
        }

        public function selectTable($name) {
            $index = $this->getTableIndexByName($name);
            if ($index != -1) {
                return $this->tables[$index];
            } else {
                throw new Exception(PLSQLException::NO_TABLE_FOUND);
            }
        }

        public function dropTable($name) {
            $index = $this->getTableIndexByName($name);
            if ($index != -1) {
                array_splice($this->tables, $index, 1);
                return true;
            } else {
                throw new Exception(PLSQLException::NO_TABLE_FOUND);
            }
        }

        public function getName() {
            return $this->name;
        }

        protected function setTables($tablesArray) {
            $tables = array();
            foreach ($tablesArray as $value) {
                $tables[] = Table::parseArray((array)$value);
            }
            $this->tables = $tables;
        }

        private function getTableIndexByName($name) {
            for ($i = 0; $i < count($this->tables); $i++) {
                $item = $this->tables[$i];
                if ($item->getName() == $name) {
                    return $i;
                }
            }
            return -1;
        }

        public static function parseArray($array) {
            $database = new Database($array["name"]);
            $database->setTables($array["tables"]);
            return $database;
        }

        public function jsonSerialize() {
            return [
                "name" => $this->name,
                "tables" => $this->tables,
            ];
        }

    }

?>


<?php

    class Table implements JsonSerializable {
        
        private $name;
        private $scheme;
        private $index;
        private $entries;

        public function __construct($name, $scheme) {
            $this->name = $name;
            $this->scheme = $scheme;
            $this->index = 0;
            $this->entries = array();
        }

        public function select($condition) {
            if (is_null($condition)) {
                return $this->entries;
            }
            $result = array();
            foreach ($this->entries as $entry) {
                $isApplies = true;
                $instance = $entry->getInstance();
                foreach ($condition as $key => $value) {
                    if ($instance[$key] != $condition[$key]) {
                        $isApplies = false;
                        break;
                    }
                }
                if ($isApplies) {
                    $result[] = $entry;
                }
            }
            return $result;
        }

        public function insert($object) {
            $this->entries[] = new Entry($this->scheme, $this->index, $object);
            $this->index++;
        }

        public function update($object) {
            for ($i = 0; $i < count($this->entries); $i++) {
                $instance = $this->entries[$i]->getInstance();
                $objectId = ((array)$object)["id"];
                if ($instance["id"] == $objectId) {
                    $this->entries[$i] = new Entry($this->scheme, $objectId, $object);
                    break;
                }
            }
        }

        public function delete($condition) {
            if (is_null($condition)) {
                array_splice($this->entries, 0, count($this->entries));
            }
            foreach ($this->entries as $index => $entry) {
                $isApplies = true;
                $instance = $entry->getInstance();
                foreach ($condition as $key => $value) {
                    if ($instance[$key] != $condition[$key]) {
                        $isApplies = false;
                        break;
                    }
                }
                if ($isApplies) {
                    unset($this->entries[$index]);
                }
            }            
        }

        public function getName() {
            return $this->name;
        }

        protected function setIndex($index) {
            $this->index = $index;
        }

        protected function setEntries($entriesArray) {
            $entries = array();
            foreach($entriesArray as $value) {
                $entries[] = new Entry($this->scheme, $this->index, ((array)$value)["instance"]);
            }
            $this->entries = $entries;
        }

        public static function parseArray($array) {
            $table = new Table($array['name'], $array['scheme']);
            $table->setIndex($array['index']);
            $table->setEntries((array)$array['entries']);
            return $table;
        }

        public function jsonSerialize() {
            return [
                "name" => $this->name,
                "scheme" => $this->scheme,
                "index" => $this->index,
                "entries" => $this->entries,
            ];
        }

    }

?>


<?php

    class Entry implements JsonSerializable {

        private $instance;

        public function __construct($scheme, $index, $object) {
            $this->instance = array(
                "id" => $index
            );
            foreach ($scheme as $key => $value) {
                $this->instance[$key] = ((array)$object)[$key];
            }
        }

        public function getInstance() {
            return $this->instance;
        }

        public function jsonSerialize() {
            return [
                "instance" => $this->instance
            ];
        }

    }

?>
