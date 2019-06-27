<?php
    include_once("plsql.php");
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

        $result = sendQuery("CREATE DATABASE `users`");

        /*$result = sendQuery("
        USE `Products`;
        SELECT * FROM `SmartPhones`;
        ");*/
    ?> 
</body>
</html>