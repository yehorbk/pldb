# Pocket Lite Database
A library that allows to use a simple database in project without installation.

# Installation
Download ```pldb.php``` file and put it to the project folder.
Then connect library to your project:
```php
include_once("pldb.php");
```

# Getting Started
Here is a simple program that shows how to create database, create table, insert and select data:

```php
<?php
  // Initializing PLDB
  $pldb = new PLDB();

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
**Yehor Bublyk**: [GitHub](https://github.com/yehorbk) • [Twitter](https://twitter.com/thisisyehorbk)
