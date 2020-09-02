<?php
    require_once './vendor/autoload.php';
    
    use PLDB\PLDBService;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PLDB Test</title>
</head>
<body>
    <?php
        // Initializing PLDB
        $pldb = new PLDBService();

        // Creating Database
        $database = $pldb->createDatabase('pldb-gs');

        // Creating Table
        $usersScheme = array( // Table scheme (field name => field type)
            "name" => "text",
            "age" => "number",
            "address" => "text",
        );
        $table = $database->createTable('users', $usersScheme);

        // Creating Users and Inserting Data to Table
        class User {
            public $name;
            public $age;
            public $address;
        }

        $john = new User();
        $john->name = "John";
        $john->age = 27;
        $john->address = "London, UK";

        $abigail = array(
            "name" => "Abigail",
            "age" => 25,
            "address" => "New York City, US",
        );

        // There is the ability to insert both an object and an array
        $table->insert($john);
        $table->insert($abigail);

        // Selecting and Printing Data
        $condition = array(
            "name" => "Abigail",
        );
        $usersArray = $table->select($condition);
        print_r($usersArray);
    ?>
</body>
</html>
