<?php
    include_once("lib/pldb.php");
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
        $pldb = new PLDB();
        /*$pldb->loadDatabase("db/pldb-test.db.json");
        $pldb->loadDatabase("db/pldb-real.db.json");
        print_r($pldb->getDatabasesNames());
        $database = $pldb->selectDatabase('pldb-real');
        $table = $database->selectTable('users');
        print_r($table->select(null));*/

        //print_r($pldb->loadDatabase("db/pldb-test.db.json"));
        //$pldb->dropDatabase("pldb-test");
        $database = $pldb->createDatabase('pldb-test');
        $sheme = array(
            "name" => "text"
        );
        $table = $database->createTable('users', $sheme);

        class User {
            public $name;
        }
        $user = new User();
        $user->name = "John";

        $user1 = array(
            "name" => "Marston"
        );

        $table->insert($user);
        $table->insert($user1);
        
        $condition = array(
            "id" => 1,
            "name" => "Doe"
        );

        //$entries = $table->select($condition);
        //print_r($entries);

        //$table->delete($condition);
        $table->update($condition);

        $entries = $table->select(null);
        //print_r($entries);
        //$database->dropTable("users");
        print_r($database->getTablesNames());
        $pldb->saveDatabase($database);

        //$table->select($condition);
        //$database = new Database("pldb-test");*/
        // $result = $database->sendQuery("CREATE DATABASE `users`");
    ?>
</body>
</html>
