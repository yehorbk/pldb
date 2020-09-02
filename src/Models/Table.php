<?php

    namespace PLDB\Models;

    use JsonSerializable;

    class Table implements JsonSerializable {
        
        private $name;
        private $scheme;
        private $index;
        private $entries;

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
                $data = $entry->getData();
                foreach ($condition as $key => $value) {
                    if ($data[$key] != $condition[$key]) {
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
                $entryId = $this->entries[$i]->getData()["id"];
                $objectId = ((array)$object)["id"];
                if ($entryId == $objectId) {
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
                $data = $entry->getData();
                foreach ($condition as $key => $value) {
                    if ($data[$key] != $condition[$key]) {
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
                    ((array)$value)["data"]);
            }
            $this->entries = $entries;
        }

        protected static function parseArray($array) {
            $table = new Table($array["name"], $array["scheme"]);
            $table->setIndex($array["index"]);
            $table->setEntriesWithArray((array)$array["entries"]);
            return $table;
        }

    }

?>
