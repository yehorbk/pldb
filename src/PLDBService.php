<?php

    namespace PLDB;

    use PLDB\Environment\PLDBConfiguration;
    use PLDB\Environment\PLDBException;

    use PLDB\Models\Database;
    use PLDB\Models\Table;
    use PLDB\Models\Entry;

    class PLDBService {

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

        public function insertDatabase($database) {
            if ($database instanceof Database) {
                $this->databases[] = $database;
                return true;
            } else {
                return false;
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
                mkdir(PLDBConfiguration::DATABASES_FOLDER, 0777, true);
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
