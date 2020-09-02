<?php

    namespace PLDB\Models;

    use JsonSerializable;

    class Entry implements JsonSerializable {

        private $data;

        public function jsonSerialize() {
            return [
                "data" => $this->data
            ];
        }

        public function __construct($scheme, $index, $object) {
            $this->data = array(
                "id" => $index,
            );
            foreach ($scheme as $key => $value) {
                $this->data[$key] = ((array)$object)[$key];
            }
        }

        public function getData() {
            return $this->data;
        }

    }

?>
