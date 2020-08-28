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
        print_r($entries);

        //$table->select($condition);
        //$database = new Database("plsql-test");
        // $result = $database->sendQuery("CREATE DATABASE `users`");
    ?>
</body>
</html>
