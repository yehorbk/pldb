# PLDB Documentation

## PLDB
`PLDB` it's the main class that provides functionality for database management.

### Creating `PLDB` instance
```php
$pldb = new PLDB();
```

### Getting list of databases names that connected to `PLDB`
Function returns the array that consists of databases names.

`array<string> PLDB::getDatabasesNames()`
```php
$databasesNames = $pldb->getDatabasesNames();
print_r($databasesNames);
```

### Selecting database
Function requires a database name and returns the `Database` instance.

`Database PLDB::selectDatabase($name)`
```php
$name = "foo-db";
$database = $pldb->selectDatabase($name);
```

### Creating database
Function requires a database name and returns the `Database` instance.
After function call the database will be created and written to file.
Also it will be added to databases array in `PLDB` instance.

`Database PLDB::createDatabase($name)`
```php
$name = "foo-db";
$database = $pldb->createDatabase($name);
```

### Droping database
After function call the database will be deleted from databases array and folder.

`boolean PLDB::dropDatabase($name)`
```php
$name = "foo-db";
$pldb->dropDatabase($name);
```

### Loading database
Function requires a database file path and returns the `Database` instance.
After loading, database will be added to databases array.

`Database PLDB::loadDatabase($path)`
```php
$path = "db/foo-db";
$database = $pldb->loadDatabase($path);
```

### Saving database
Function requires a `Database` instance.
After function call the database file will be created in databases folder.

Do not forget to save databases after changing them.

`void PLDB::saveDatabase($database)`
```php
$pldb->saveDatabase($database);
```

## Database
`Database` class provides a table management.

### Creating `Database` instance
Function requires a database name and returns the `Database` instance.
Database will not be added to `PLDB` instanse instead of `PLDB::createDatabase` function.

```php
$name = "foo-db";
$database = new Database($name);
```

### Getting list of tables names that contains in database
Function returns the array that consists of tables names.

`array<string> Database::getTablesNames()`
```php
$tablesNames = $database->getTablesNames();
print_r($tablesNames);
```

### Selecting table
Function requires a table name and returns the `Table` instance. 

`Table Database::selectTable($name)`
```php
$name = 'users';
$table = $database->selectTable($name);
```

### Creating table
Function requires a table name and returns the `Table` instance.
After function call the table will be created and added to the tables list in database.

The `scheme` it's a key-value array that contains names of fields and their types (JSON Types).
Do not add an `id` field to scheme, because it will be added later automatically.

`Table Database::createTable($name, $scheme)`
```php
$name = "users";
$scheme = array(
  "name" => "text",
  "age" => "number",
);
$table = $database->createTable($name, $scheme);
```

### Droping Table
After function call the table will be deleted from database tables array.

`boolean Database::dropTable($name)`
```php
$name = "users";
$database->dropTable($name);
```

### Getting database name from instance
Function returs the name of database.

`string Database::getName()`
```php
$name = $database->getName();
echo $name;
```

## Table
`Table` class allows to work with the table data.

### Creating `Table` instance
Function requires a table name and scheme, and returns the `Table` instance.
Table will not be added to `Database` instanse instead of `Database::createTable`.

```php
$name = "users";
$scheme = array(
  "name" => "text",
  "age" => "number",
);
$table = new Table($name, $scheme);
```

### Selecting data from table
Function requires a condition and returns the array of `Entry` instanses.

The `condition` it's a key-value array that contains fields and their values.

`array<Entry> Table::select($condition)`
```php
$condition = array(
  "name" => "John",
);
$data = $table->select($condition);
print_r($data);
```

### Inserting data to table
Function requires an object that will be converted to Entry and added to the entries array.

Feel free to pass to the function both an object and an array. The object firstly will be converted to array and then to `Entry`.

`void Table::insert($object)`
```php
$object = Array(
  "name" => "John",
  "age" => 27,
);
$table->insert($object);
```

### Updating data in table
Function requires an object that contains an entry id. 

If there is no id in object the data will not be updated.

`boolean Table::update($object)`
```php
$object = Array(
  "id" => 0,
  "name" => "John",
  "age" => 27,
);
$table->update($object);
```

### Deleting data from table
Function requires a condition. 

Only the first found entry will be deleted.

`void Table::delete($condition)`
```php
$condition = array(
  "name" => "John",
);
$table->delete($condition);
```

### Getting table name from instance
Function returs the name of table.

`string Table::getName()`
```php
$name = $table->getName();
echo $name;
```

## Entry
`Entry` class makes it easier to work with data in table.

### Getting the data from instance
Function returns the key-value array with the data.

`array<string, string> Entry::getInstance()`
```php
$data = $entry->getInstance();
print_r($data);
```
