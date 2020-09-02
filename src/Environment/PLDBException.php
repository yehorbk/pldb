<?php

    namespace PLDB\Environment;

    class PLDBException {
        const DATABASE_EXISTS = "Database already exists!";
        const NO_DATABASE_FOUND = "There is no such database!";
        const TABLE_EXISTS = "Table already exists!";
        const NO_TABLE_FOUND = "There is no such table!";
        const CANNOT_OPEN_FILE = "Cannot open file!";
    }

?>
