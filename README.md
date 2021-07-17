
# SQL Module for PHP

Quickly Manage SQL Functions in PHP
## Functions Available
1. [executeQuery] (#executeQuery)
2. [fetchData] (#fetchData)
3. [fetchList] (#fetchList)
4. [isSQLDataExist] (#isSQLDataExist)
5. [isSQLListEmpty] (#isSQLListEmpty)
6. [countRows] (#countRows)
7. [countRowsInTable] (#countRowsInTable)
8. [isTableEmpty] (#isTableEmpty)
9. [createTable] (#createTable)
10. [insertNewColumn] (#insertNewColumn)
11. [editColumn] (#editColumn)
12. [deleteColumn] (#deleteColumn)
13. [deleteTable] (#deleteTable)
14. [emptyTable] (#emptyTable)
15. [insertRow] (#insertRow)
16. [updateRow] (#updateRow)
17. [sqlPrimaryKey] (#sqlPrimaryKey)
18. [sqlUniqueKey] (#sqlUniqueKey)
19. [setPHPErrors] (#setPHPErrors)

## Usage/Examples

## executeQuery
#### Executes a Raw SQL Statement

```php
executeQuery("DELETE FROM TESTDB WHERE id = 1");
```

## fetchData
#### Returns a Row of data from the SQL Statement Provided

```php
fetchData("SELECT * FROM TESTDB");
fetchData("SELECT * FROM TESTDB", false); // may cause errors due to incorrect sql statement, which returns multiple rows
```


## fetchList
#### Get a list of items returned from a SQL Query

```php
fetchList("SELECT * FROM TESTDB");
```



## isSQLDataExist
#### Checks if data exists with FetchData method.

```php
isSQLDataExist("SELECT * FROM TESTDB");
```



## isSQLListEmpty
#### Checks if the list from SQL Query is empty or not

```php
isSQLListEmpty("SELECT * FROM TESTDB");
```



## countRows
#### Counts the Number of Rows Returned by a SQL Statement

```php
countRows("SELECT * FROM TESTDB");
```




## countRowsInTable
#### Counts the Number of Rows in The Table

```php
countRowsInTable("tableName");
```


## isTableEmpty
#### Checks if the table is empty or not

```php
isTableEmpty("tableName");
```




## createTable
#### Create a New Table into the Database

```php
$createVariable = new createTable("tableName");
$createVariable->createColumn($columnName, $dataType, $size = 0, $defaultValue = "", $canBeNull = true, $isPrimaryKey = false, $autoIncrement = false);

$createVariable->createColumnWithSql("CREATE TABLE SQL"); //optional

$createVariable->createTable(); // returns boolean
```


## insertNewColumn
#### Inserts a New Column into a previously made Table

```php
insertNewColumn($tableName, $newColumnName, $dataType, $size = 0, $defaultValue = "", $canBeNull = true, $isPrimaryKey = false, $autoIncrement = false, $insertAfter = "LAST");
```
  

  

## editColumn
#### Inserts a New Column into a previously made Table

```php
editColumn($tableName, $columnName, $newColumnName = "", $dataType, $size = 0, $defaultValue = "", $canBeNull = true, $isPrimaryKey = false, $autoIncrement = false, $insertAfter = "LAST");
```


 

## deleteColumn
#### Deletes a Column from a Table

```php
deleteColumn($tableName, $columnName);
```



## deleteTable
#### Deletes a Table from Database

```php
deleteTable($tableName);
```




## emptyTable
#### Empty a Table in the Database

```php
emptyTable($tableName);
```




## insertRow
#### Insert a New Row into the Table

```php
$insertVariable = new insertRow("tableName");
$insertVariable->addColumnData($columnName, $value);
$insertVariable->insertRow();
```


## updateRow
#### Update a Row in the Table

```php
$updateVariable = new updateRow("tableName");
$updateVariable->addColumnData($columnName, $value);
$updateVariable->updateRow($condition);
```



## sqlPrimaryKey
#### Add Primary Key to the table

```php
$sqlPrimaryKey = new sqlPrimaryKey("tableName");
$sqlPrimaryKey->addPrimaryKey($columnName);
$sqlPrimaryKey->setKeys();
```



## sqlUniqueKey
#### Add Unique Key to the table

```php
$sqlUniqueKey = new sqlUniqueKey("tableName");
$sqlUniqueKey->addUniqueKey($columnName);
$sqlUniqueKey->setKeys();
```



## setPHPErrors
#### Show or Display Errors/Warnings in PHP

```php
setPHPErrors(false); //hide all errors & warnings
```