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
                print_r($this->tables);
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
                print_r($this->tables);
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

    class Table {
        
        private $name;
        private $scheme;
        private $fields;

        function __construct($name, $scheme) {
            $this->name = $name;
            $this->scheme = $scheme;
            $this->fields = array();
        }

        function select() {

        }

        function insert($object) {

        }

        function update() {

        }

        function delete() {

        }

        function getName() {
            return $this->name;
        }

    }

    class Entry {

    }

?>
