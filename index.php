<?php
    include_once("lib/plsql.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PLSQL Test</title>
</head>
<body>
    <?php
        $plsql = new PLSQL();
        $database = $plsql->createDatabase('plsql-test');
        $sheme = array(
            "id" => "int",
            "name" => "text"
        );
        $table = $database->createTable('users', $sheme);

        class User {
            public $id;
            public $name;
        }
        $user = new User();
        $user->id = 0;
        $user->name = "John";

        $user1 = array(
            "id" => 0,
            "name" => "Marston"
        );

        $table->insert($user);
        $table->insert($user1);
        
        $condition = array(
            "name" => "Marston"
        );

        //$entries = $table->select($condition);
        //print_r($entries);

        $table->delete($condition);
        $entries = $table->select(null);
        print_r($entries);

        //$table->select($condition);
        //$database = new Database("plsql-test");
        // $result = $database->sendQuery("CREATE DATABASE `users`");
    ?>
</body>
</html>
