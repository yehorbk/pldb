<?php

    class PLSQL {

        private $databases;

        function __construct() {
            $this->databases = array();
        }

        function createDatabase($name) {
            if ($this->getDatabaseIndexByName($name) == -1) {
                $database = new Database($name);
                $this->databases[] = $database;
                return $database;
            } else {
                // Throw Error
                echo "Database already exists!";
            }
        }

        function dropDatabase($name) {
            $index = $this->getDatabaseIndexByName($name);
            if ($index != -1) {
                array_splice($this->databases, $index, 1);
                return true;
            } else {
                // Throw Error
                echo "There is no such database";
            }
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

    class Database {

        private $name;
        private $tables;

        function __construct($name) {
            $this->name = $name;
            $this->tables = array();
        }

        function createTable($name, $scheme) {
            if ($this->getTableIndexByName($name) == -1) {
                $table = new Table($name, $scheme);
                $this->tables[] = $table;
                return $table;
            } else {
                // Throw Error
                echo "Table already exists!";
            }
        }

        function dropTable($name) {
            $index = $this->getTableIndexByName($name);
            if ($index != -1) {
                array_splice($this->tables, $index, 1);
                return true;
            } else {
                // Throw Error
                echo "There is no such table";
            }
        }

        function getName() {
            return $this->name;
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

    class Table {
        
        private $name;
        private $scheme;
        private $index;
        private $entries;

        function __construct($name, $scheme) {
            $this->name = $name;
            $this->scheme = $scheme;
            $this->index = 0;
            $this->entries = array();
        }

        function select($condition) {
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

        function insert($object) {
            $this->entries[] = new Entry($this->scheme, $this->index, $object);
            $this->index++;
        }

        function update($object) {
            for ($i = 0; $i < count($this->entries); $i++) {
                $instance = $this->entries[$i]->getInstance();
                $objectId = ((array)$object)["id"];
                if ($instance["id"] == $objectId) {
                    $this->entries[$i] = new Entry($this->scheme, $objectId, $object);
                    break;
                }
            }
        }

        function delete($condition) {
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

        function getName() {
            return $this->name;
        }

    }

?>

<?php

    class Entry {

        private $instance;

        function __construct($scheme, $index, $object) {
            $this->instance = array(
                "id" => $index
            );
            foreach ($scheme as $key => $value) {
                $this->instance[$key] = ((array)$object)[$key];
            }
        }

        function getInstance() {
            return $this->instance;
        }

    }

?>
