
# SQL Module for PHP

Quickly Manage SQL Functions in PHP
## Functions Available
1. [executeQuery](#executeQuery)
2. [fetchData](#fetchData)
3. [fetchList](#fetchList)
4. [isSQLDataExist](#isSQLDataExist)
5. [isSQLListEmpty](#isSQLListEmpty)
6. [countRows](#countRows)
7. [countRowsInTable](#countRowsInTable)
8. [isTableEmpty](#isTableEmpty)
9. [createTable](#createTable)
10. [insertNewColumn](#insertNewColumn)
11. [editColumn](#editColumn)
12. [deleteColumn](#deleteColumn)
13. [deleteTable](#deleteTable)
14. [emptyTable](#emptyTable)
15. [insertRow](#insertRow)
16. [updateRow](#updateRow)
17. [sqlPrimaryKey](#sqlPrimaryKey)
18. [sqlUniqueKey](#sqlUniqueKey)
19. [setPHPErrors](#setPHPErrors)
20. [isTablePresent](#isTablePresent)
21. [uploadFile](#uploadFile)

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


## isTablePresent
#### Check if the Table exists in the database

```php
isTablePresent($tableName); //Returns true if the Table exists in the database. Otherwise returns false.
```



## uploadFile
#### Upload a File and Save it

```php
$uploadImage = new uploadFile();

$uploadImage->setDirectory("uploads/"); //set directory where the file should be uploaded.

$uploadImage->setMaxSize(10); //Set Maximum Size of the File to be uploaded (in MB). Set 0 for unlimited size.

$uploadImage->setFileFormats(array("jpg", "png", "jpeg", "gif")); //List of file formats to be accepted for Upload. Set Empty array for all File formats.

$uploadImage->setReplaceFile(true); //Set True to replace the existing file. Otherwise set False. Default is false.

$uploadImage->setFileName("myimage2.".$uploadImage->getFileExtension($_FILES['imgfile'])); //set a custom file name.

$uploadImage->setReplaceFile(true); //Replace the existing file with same name.

$uploadImage->compressImage(true, $compression_quality, $keep_original); // (optional) Compress the image. 
/* 
Compression Quality should be Minimum 0 (Lowest compression quality) and Maximum 100 (highest compression quality). 
If Keep Original is set to true, only one file gets saved. Otherwise Both original and compressed files are saved.
*/

echo $uploadImage->setCompressedImageDirectory("uploads/compressed/"); //(optional) The Relative File Directory where the Compressed Image is stored.

echo $uploadImage->uploadFile($_FILES['imgfile'], true); //Upload the file

echo $uploadImage->getFileLink(); //Get the Relative file Link of the uploaded file.
echo $uploadImage->getCompressedImageLink(); //Get the Relative file Link of the compressed Image.
```