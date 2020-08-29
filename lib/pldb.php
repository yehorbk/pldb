<?php

    class PLDBException {
        const DATABASE_EXISTS = "Database already exists!";
        const NO_DATABASE_FOUND = "There is no such database!";
        const TABLE_EXISTS = "Table already exists!";
        const NO_TABLE_FOUND = "There is no such table!";
        const CANNOT_OPEN_FILE = "Cannot open file!";
    }

    class PLDBConfiguration {
        const DATABASES_FOLDER = "db";
        const DATABASE_TYPE = "db.json";
    }

?>


<?php

    class PLDB {

        private $databases;

        public function __construct() {
            $this->databases = array();
            $this->createDatabasesFolder();
        }

        public function getDatabasesNames() {
            $result = array();
            foreach ($this->databases as $database) {
                $result[] = $database->getName();
            }
            return $result;
        }

        public function selectDatabase($name) {
            $index = $this->getDatabaseIndexByName($name);
            if ($index != -1) {
                return $this->databases[$index];
            } else {
                throw new Exception(PLDBException::NO_DATABASE_FOUND);
            }
        }

        public function createDatabase($name) {
            if ($this->getDatabaseIndexByName($name) == -1) {
                $database = new Database($name);
                $this->databases[] = $database;
                $this->saveDatabase($database);
                return $database;
            } else {
                throw new Exception(PLDBException::DATABASE_EXISTS);
            }
        }

        public function dropDatabase($name) {
            $index = $this->getDatabaseIndexByName($name);
            if ($index != -1) {
                array_splice($this->databases, $index, 1);  
                unlink($this->prepareFilePath($name));
                return true;
            } else {
                throw new Exception(PLDBException::NO_DATABASE_FOUND);
            }
        }

        public function loadDatabase($path) {
            $file = fopen($path, "r") or die (PLDBException::CANNOT_OPEN_FILE);
            $content = fread($file, filesize($path));
            $database = Database::parseArray((array)json_decode($content));
            if ($this->getDatabaseIndexByName($database->getName()) == -1) {
                $this->databases[] = $database;
                return $database;
            } else {
                throw new Exception(PLDBException::DATABASE_EXISTS);
            }
            fclose($file);
        }

        public function saveDatabase($database) {
            $content = json_encode($database);
            $file = fopen($this->prepareFilePath($database->getName()), "w") or
                die (PLDBException::CANNOT_OPEN_FILE);
            fwrite($file, $content);
            fclose($file);
        }

        private function createDatabasesFolder() {
            if(!file_exists(PLDBConfiguration::DATABASES_FOLDER)) {
                mkdir($path, 0777, true);
            }
        }

        private function prepareFilePath($name) {
            $file = $name.".".PLDBConfiguration::DATABASE_TYPE;
            return PLDBConfiguration::DATABASES_FOLDER."/".$file;
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

        public static function parseArray($array) {
            $database = new Database($array["name"]);
            $database->setTablesWithArray($array["tables"]);
            return $database;
        }

        public function jsonSerialize() {
            return [
                "name" => $this->name,
                "tables" => $this->tables,
            ];
        }

        public function __construct($name) {
            $this->name = $name;
            $this->tables = array();
        }

        public function getTablesNames() {
            $result = array();
            foreach ($this->tables as $table) {
                $result[] = $table->getName();
            }
            return $result;
        }

        public function selectTable($name) {
            $index = $this->getTableIndexByName($name);
            if ($index != -1) {
                return $this->tables[$index];
            } else {
                throw new Exception(PLDBException::NO_TABLE_FOUND);
            }
        }

        public function createTable($name, $scheme) {
            if ($this->getTableIndexByName($name) == -1) {
                $table = new Table($name, $scheme);
                $this->tables[] = $table;
                return $table;
            } else {
                throw new Exception(PLDBException::TABLE_EXISTS);
            }
        }

        public function dropTable($name) {
            $index = $this->getTableIndexByName($name);
            if ($index != -1) {
                array_splice($this->tables, $index, 1);
                return true;
            } else {
                throw new Exception(PLDBException::NO_TABLE_FOUND);
            }
        }

        public function getName() {
            return $this->name;
        }

        protected function setTablesWithArray($tablesArray) {
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

    }

?>


<?php

    class Table implements JsonSerializable {
        
        private $name;
        private $scheme;
        private $index;
        private $entries;

        public static function parseArray($array) {
            $table = new Table($array["name"], $array["scheme"]);
            $table->setIndex($array["index"]);
            $table->setEntriesWithArray((array)$array["entries"]);
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
            $this->entries[] = new Entry($this->scheme,
                $this->index, $object);
            $this->index++;
        }

        public function update($object) {
            for ($i = 0; $i < count($this->entries); $i++) {
                $instanceId = $this->entries[$i]->getInstance()["id"];
                $objectId = ((array)$object)["id"];
                if ($instanceId == $objectId) {
                    $this->entries[$i] = new Entry($this->scheme,
                        $objectId, $object);
                    return true;
                }
            }
            return false;
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

        protected function setEntriesWithArray($entriesArray) {
            $entries = array();
            foreach ($entriesArray as $value) {
                $entries[] = new Entry($this->scheme, $this->index,
                    ((array)$value)["instance"]);
            }
            $this->entries = $entries;
        }

    }

?>


<?php

    class Entry implements JsonSerializable {

        private $instance;

        public function jsonSerialize() {
            return [
                "instance" => $this->instance
            ];
        }

        public function __construct($scheme, $index, $object) {
            $this->instance = array(
                "id" => $index,
            );
            foreach ($scheme as $key => $value) {
                $this->instance[$key] = ((array)$object)[$key];
            }
        }

        public function getInstance() {
            return $this->instance;
        }

    }

?>
