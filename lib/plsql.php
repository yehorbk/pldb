<?php

    class PLSQL {

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

    }

    class Table {
        
        private $name;
        private $scheme;
        private $fields;

        function __construct($name, $scheme) {
            $this->name = $name;
            $this->scheme = $scheme;
        }

    }

    class Entry {

    }

?>
