# Pocket Lite Database
A library that provides functionality for working with simple self-contained databases.

# Concept
The main idea of the project is to allow programmers of any level to use the database for simple purposes without the need to install and configure it.

# Installation
Use Composer to install PLDB into your project:
```
$ composer require yehorbk/pldb
```

# Getting Started
Here is a simple program that shows how to create database, create table, insert and select data:

```php
<?php

  // Initializing PLDBService
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
```

# Documentation
For more information about library api - check the [docs](https://github.com/yehorbk/PLSQL/blob/master/DOCUMENTATION.md).

# Author
**Yehor Bublyk**: [GitHub](https://github.com/yehorbk) • [Twitter](https://twitter.com/yehorbk)
