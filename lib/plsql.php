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
                print_r($this->databases);
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
            print_r($this->databases);
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
            $this->tables[] = new Table($name, $scheme);
        }

        function dropTable($name) {

        }

        function getName() {
            return $this->name;
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

    }

    class Entry {

    }

?>
