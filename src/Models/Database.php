<?php

    namespace PLDB\Models;

    use JsonSerializable;

    class Database implements JsonSerializable {

        private $name;
        private $tables;

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

        public function insertTable($table) {
            if ($table instanceof Table) {
                $this->tables[] = $table;
                return true;
            } else {
                return false;
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

        protected static function parseArray($array) {
            $database = new Database($array["name"]);
            $database->setTablesWithArray($array["tables"]);
            return $database;
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
