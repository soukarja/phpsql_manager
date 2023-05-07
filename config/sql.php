<?php

require_once __DIR__ . '/config.php';

/**
 * Different Types of SQL Data Types that can be passed within Functions if Required
 */

$sqlDataTypes = (object)[];

// Numeric
$sqlDataTypes->INT = "INT";
$sqlDataTypes->DECIMAL = "DECIMAL";
$sqlDataTypes->FLOAT = "FLOAT";
$sqlDataTypes->DOUBLE = "DOUBLE";
$sqlDataTypes->REAL = "REAL";

$sqlDataTypes->BIT = "BIT";
$sqlDataTypes->BOOLEAN = "BOOLEAN";

//Date and Time
$sqlDataTypes->DATETIME = "DATETIME";
$sqlDataTypes->DATE = "DATE";
$sqlDataTypes->TIME = "TIME";
$sqlDataTypes->TIMESTAMP = "TIMESTAMP";
$sqlDataTypes->YEAR = "YEAR";

//String
$sqlDataTypes->TEXT = "TEXT";
$sqlDataTypes->VARCHAR = "VARCHAR";
$sqlDataTypes->CHAR = "CHAR";
$sqlDataTypes->LONGTEXT = "LONGTEXT";


$sqlOperationTypes = (object)[];
$sqlOperationTypes->SELECT = "SELECT";
$sqlOperationTypes->UPDATE = "UPDATE";
$sqlOperationTypes->DELETE = "DELETE";
$sqlOperationTypes->ALTER = "ALTER";


/** 
 * Executes a Raw SQL Statement
 * @param string $sql The sql String command to be executed
 * @return bool Returns true if the query is successfully executed. Otherwise returns false.
 */
function executeQuery($sql)
{
    $link = connectSQL();

    if ($stmt = mysqli_prepare($link, $sql)) {

        if (mysqli_stmt_execute($stmt)) {

            return true;
        } else {
            return false;
        }
        mysqli_stmt_close($stmt);
    }
}



/**
 * Returns a Row of data from the SQL Statement Provided
 * @param string $SQL The SQL String command to be executed
 * @param bool $safequery (optional) - Limits the Search List to 1 Result. Use false if you are using LIMIT in SQL query. Default is true.
 * 
 * @return array Returns an Array of the data contained from SQL
 * 
 */
function fetchData($sql, $safequery = true)
{
    $link = connectSQL();

    if ($safequery && !(strpos($sql, ' LIMIT ') !== false))
        $sql = trim($sql) . " LIMIT 1";

    $query = $link->query($sql);
    $row = $query->fetch_array();
    return $row;
}

/**
 * Checks if data exists with FetchData method.
 * @param string $SQL The SQL String command to be executed
 * @param bool $safequery (optional) - Limits the Search List to 1 Result. Use false if you are using LIMIT in SQL query. Default is true.
 * 
 * @return bool Returns true if data exists with FetchData method. Otherwise returns false
 * 
 */
function isSQLDataExist($sql, $safequery = true)
{
    return isset(fetchData($sql, $safequery)[0]);
}


/**
 * Get a list of items returned from a SQL Query
 * @param string $sql The SQL String command to be executed
 * @return object Returns an Object of the data contained from SQL. Use loop to extract all data
 * 
 */
function fetchList($sql)
{
    $link = connectSQL();
    $query = $link->query($sql);

    return $query;
}



/**
 * Checks if the list from SQL Query is empty or not
 * @param string $sql The SQL String command to be executed
 * @return bool Returns true if the list from SQL Query is empty. Otherwise returns false.
 * 
 */
function isSQLListEmpty($sql)
{
    if (countRows($sql) <= 0)
        return true;

    return false;
}


/**
 * Counts the Number of Rows Returned by a SQL Statement
 * @param string $sql The sql Command to be executed
 * @return int Returns the number of rows returned through the query
 */

function countRows($sql)
{
    return fetchList($sql)->num_rows;
}


/**
 * Counts the Number of Rows in The Table
 * @param string $tableName The Name of the table
 * @return int Returns the number of rows present in the table
 */
function countRowsInTable($tableName)
{
    return countRows("SELECT * FROM `$tableName`");
}

/**
 * Checks if the table is empty or not
 * @param string $tableName The Name of the table
 * @return bool Returns true if the table is empty. Otherwise Returns false.
 */
function isTableEmpty($tableName)
{
    if (countRowsInTable($tableName) <= 0)
        return true;

    return false;
}

/**
 * Create a New Table into the Database
 */
class createTable
{
    private $vars;
    private $tableName;

    /**
     * @param string $tableName Name of the Table
     */
    function __construct($tableName = "")
    {
        $this->tableName = $tableName;
        $this->vars = [];
    }

    /**
     * Sets the Name of the Table to Be created
     * @param string $tableName Name of the Table
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }


    /**
     * Creates a New Column in the New Table to be Created
     * @param string $columnName Name of the Column (Required)
     * @param string $dataType Set Data Type of the Column. Example: INT, TEXT, DATETIME (Required)
     * @param int $size Set the Max Size of Data in Column. Set 0 for undefined. (Optional) [Default = 0]
     * @param string $defaultValue Set the Default Value of Data in Column.(Optional)
     * @param bool $canBeNull Sets if the column can ever be null. (Optional) [Default = true]
     * @param bool $isPrimaryKey Sets if the Column is a Primary Key (Optional) [Default = false]
     * @param bool $autoIncrement Sets if the Column gets Auto Incremented (Optional) [Default = false]
     */
    public function createColumn($columnName, $dataType, $size = 0, $defaultValue = "", $canBeNull = true, $isPrimaryKey = false, $autoIncrement = false)
    {
        $sqlCommand = "`" . $columnName . "` " . $dataType;


        if ($dataType == "TEXT")
            $size = 0;

        if ($size > 0) {
            $sqlCommand .= "($size)";
        }

        if (!$canBeNull) {
            $sqlCommand .= " NOT NULL";
        }

        if (trim($defaultValue) != "")
            $sqlCommand .= " DEFAULT '$defaultValue'";

        if ($autoIncrement) {
            $sqlCommand .= " AUTO_INCREMENT";
        }

        if ($isPrimaryKey) {
            $sqlCommand .= " PRIMARY KEY";
        }

        array_push($this->vars, $sqlCommand);
    }

    /**
     * Create a New Column in the Table to be created, with Raw SQL Command
     */

    public function createColumnWithSql($sql)
    {
        array_push($this->vars, $sql);
    }

    /**
     * Creates the New Table into the Database
     * @return bool Returns true if the Table is Created. Otherwise returns false.
     */
    public function createTable()
    {

        if (trim($this->tableName) == "")
            return false;

        if (count($this->vars) <= 0)
            return false;



        $sqlCommand = "";


        foreach ($this->vars as $v) {
            if ($sqlCommand != "") {
                $sqlCommand .= ", ";
            }

            $sqlCommand .= $v;
        }

        $sqlCommand = "CREATE TABLE `" . trim($this->tableName) . "` (" . $sqlCommand . ")";

        return executeQuery($sqlCommand);
    }
}

/**
 * Inserts a New Column into a previously made Table
 * @param string $tableName Name of the Table (Required)
 * @param string $newColumnName Name of the New Column (Required)
 * @param string $dataType Set Data Type of the Column. Example: INT, TEXT, DATETIME (Required)
 * @param int $size Set the Max Size of Data in Column. Set 0 for undefined. (Optional) [Default = 0]
 * @param string $defaultValue Set the Default Value of Data in Column.(Optional)
 * @param bool $canBeNull Sets if the column can ever be null. (Optional) [Default = true]
 * @param bool $isPrimaryKey Sets if the Column is a Primary Key (Optional) [Default = false]
 * @param bool $autoIncrement Sets if the Column gets Auto Incremented (Optional) [Default = false]
 * @param string $insertAfter Position of the New Column to be inserted After. (Optional) [Default = "LAST"] [Values="LAST", "FIRST", another table column name]
 * 
 * @return bool Returns true if the New Column is inserted into the Table. Otherwise Returns false.
 */

function insertNewColumn($tableName, $newColumnName, $dataType, $size = 0, $defaultValue = "", $canBeNull = true, $isPrimaryKey = false, $autoIncrement = false, $insertAfter = "LAST")
{
    if (trim($tableName) == "")
        return false;

    if (trim($newColumnName) == "")
        return false;

    if (trim($dataType) == "")
        return false;

    $sqlCommand = "ALTER TABLE `" . trim($tableName) . "` ADD `" . $newColumnName . "` $dataType";

    if ($dataType == "TEXT")
        $size = 0;

    if ($size > 0) {
        $sqlCommand .= "($size)";
    }

    if (!$canBeNull) {
        $sqlCommand .= " NOT NULL";
    }

    if ($autoIncrement) {
        $sqlCommand .= " AUTO_INCREMENT";
    }

    if (trim($defaultValue) != "")
        $sqlCommand .= " DEFAULT '$defaultValue'";

    if ($isPrimaryKey) {
        $sqlCommand .= " PRIMARY KEY";
    }

    if (trim(strtolower($insertAfter)) != "last") {
        if (trim(strtolower($insertAfter)) == "first") {
            $sqlCommand .= " FIRST";
        } else {
            $sqlCommand .= " AFTER `$insertAfter`";
        }
    }

    return executeQuery($sqlCommand);
}


/**
 * Edits the Column Properties in the Table
 * @param string $tableName Name of the Table (Required)
 * @param string $columnName Name of the Column (Required)
 * @param string $dataType Set Data Type of the Column. Example: INT, TEXT, DATETIME (Required)
 * @param string $newColumnName Name of the New Column (Optional)
 * @param int $size Set the Max Size of Data in Column. Set 0 for undefined. (Optional) [Default = 0]
 * @param string $defaultValue Set the Default Value of Data in Column.(Optional)
 * @param bool $canBeNull Sets if the column can ever be null. (Optional) [Default = true]
 * @param bool $isPrimaryKey Sets if the Column is a Primary Key (Optional) [Default = false]
 * @param bool $autoIncrement Sets if the Column gets Auto Incremented (Optional) [Default = false]
 * @param string $insertAfter Position of the New Column to be inserted After. (Optional) [Default = "LAST"] [Values="LAST", "FIRST", another table column name]
 * 
 * @return bool Returns true if the Column is changed in the Table. Otherwise Returns false.
 */

function editColumn($tableName, $columnName, $dataType, $newColumnName = "", $size = 0, $defaultValue = "", $canBeNull = true, $isPrimaryKey = false, $autoIncrement = false, $insertAfter = "LAST")
{
    if (trim($tableName) == "")
        return false;

    if (trim($columnName) == "")
        return false;

    if (trim($dataType) == "")
        return false;

    if (trim($newColumnName) == "")
        $newColumnName = $columnName;

    $sqlCommand = "ALTER TABLE `" . $tableName . "` CHANGE `" . $columnName . "` `$newColumnName` $dataType";

    if ($dataType == "TEXT")
        $size = 0;

    if ($size > 0) {
        $sqlCommand .= "($size)";
    }

    if (!$canBeNull) {
        $sqlCommand .= " NOT NULL";
    }

    if ($autoIncrement) {
        $sqlCommand .= " AUTO_INCREMENT";
    }

    if (trim($defaultValue) != "")
        $sqlCommand .= " DEFAULT '$defaultValue'";

    if ($isPrimaryKey) {
        $sqlCommand .= " PRIMARY KEY";
    }

    if (trim(strtolower($insertAfter)) != "last") {
        if (trim(strtolower($insertAfter)) == "first") {
            $sqlCommand .= " FIRST";
        } else {
            $sqlCommand .= " AFTER `$insertAfter`";
        }
    }

    return executeQuery($sqlCommand);
}

/**
 * Deletes a Column from a Table
 * 
 * @param string $tableName Name of the Table
 * @param string $columnName Name of the Column to be Deleted
 * 
 * @return bool Returns true if the Column is deleted from the Table. Otherwise Returns false.
 */
function deleteColumn($tableName, $columnName)
{
    if (trim($tableName) == "")
        return false;

    if (trim($columnName) == "")
        return false;

    return executeQuery("ALTER TABLE `$tableName` DROP `$columnName`");
}


/**
 * Deletes a Table from Database
 * 
 * @param string $tableName Name of the Table
 * @return bool Returns true if the Table is deleted. Otherwise Returns false.
 */
function deleteTable($tableName)
{
    if (trim($tableName) == "")
        return false;

    return executeQuery("DROP TABLE `$tableName`");
}

/**
 * Empty a Table in the Database
 * 
 * @param string $tableName Name of the Table
 * @return bool Returns true if the Table is emptied. Otherwise Returns false.
 */

function emptyTable($tableName)
{
    if (trim($tableName) == "")
        return false;

    return executeQuery("TRUNCATE `$tableName`");
}

/**
 * Insert a New Row into the Table
 */
class insertRow
{
    private $tableName;
    private $columnNames;
    private $columnValues;

    /**
     * @param string $tableName Name of the Table
     */

    function __construct($tableName = "")
    {
        $this->tableName = $tableName;
        $this->columnNames = [];
        $this->columnValues = [];
    }

    /**
     * Sets the Name of the Table to Be created
     * @param string $tableName Name of the Table
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Add Data for each Column
     * @param string $columnName Name of the Column
     * @param string $value The Value to be inserted into the column
     */
    public function addColumnData($columnName, $value)
    {
        array_push($this->columnNames, $columnName);
        array_push($this->columnValues, $value);
    }

    /**
     * Add Values in the Table for all Columns within it
     * @param Array $value String Array containing Values for all Columns in the Table in Order
     */
    public function addColumnValues($values)
    {
        $this->columnValues = $values;
        $this->columnNames = [];
    }


    /**
     * Insert Row into the Table.
     * @return bool returns true if the Row is Inserted. Otherwise Returns false.
     */

    public function insertRow()
    {

        if (trim($this->tableName) == "")
            return false;


        $sqlCommand = "INSERT INTO `" . $this->tableName . "`";

        $temp = "";
        foreach ($this->columnNames as $cN) {
            if ($temp != "")
                $temp .= ", ";
            else
                $temp = " (";

            $temp .= "`$cN`";
        }

        if ($temp != "")
            $temp .= ")";

        $sqlCommand .= $temp;

        $temp = "";
        foreach ($this->columnValues as $cN) {
            if ($temp != "")
                $temp .= ", ";
            else
                $temp = " VALUES (";

            $temp .= "'$cN'";
        }

        if ($temp != "")
            $temp .= ")";

        $sqlCommand .= $temp;



        return executeQuery($sqlCommand);
    }
}


/**
 * Update a Row in the Table
 */
class updateRow
{
    private $tableName;
    private $condition;
    private $columnNames;
    private $columnValues;

    /**
     * @param string $tableName Name of the Table
     */

    function __construct($tableName = "")
    {
        $this->tableName = $tableName;
        $this->columnNames = [];
        $this->columnValues = [];
        $this->condition = "";
    }

    /**
     * Sets the Name of the Table to Be updated
     * @param string $tableName Name of the Table
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Add Data for each Column tom be updated
     * @param string $columnName Name of the Column
     * @param string $value The Value to be updated into the column
     */
    public function addColumnData($columnName, $value)
    {
        array_push($this->columnNames, $columnName);
        array_push($this->columnValues, $value);
    }

    /**
     * Set Update Condition for the Query
     * @param string $condition Condition for the SQL Update Statement
     */
    public function setCondition($condition)
    {
        $this->condition = trim($condition);
    }


    /**
     * Update the Row in the Table.
     * @return bool returns true if the Row is Updated. Otherwise Returns false.
     */

    public function updateRow($condition = "")
    {
        if (trim($condition) != "")
            $this->condition = trim($condition);

        if (trim($this->tableName) == "")
            return false;

        if (count($this->columnNames) <= 0)
            return false;

        if (count($this->columnValues) <= 0)
            return false;

        if (count($this->columnValues) != count($this->columnNames))
            return false;


        $sqlCommand = "UPDATE `" . $this->tableName . "`";

        $temp = "";

        for ($i = 0; $i < count($this->columnNames); $i++) {
            if ($temp != "")
                $temp .= ", ";
            else
                $temp = " SET ";

            $temp .= "`" . $this->columnNames[$i] . "` = '" . $this->columnValues[$i] . "'";
        }



        $sqlCommand .= $temp;

        if (!empty(trim($this->condition))) {
            $sqlCommand .= " " . $this->condition;
        }

        return executeQuery($sqlCommand);
    }
}

// class sqlSELECT{
//     private $conditions;
//     private $tableName;
//     private $valuesToSelect;
//     private $limits;
//     private $orderBy;
//     public $orderTypes;

//     function __construct($tableName="", $valuesToSelect="*"){
//         $this->tableName = $tableName;
//         $this->conditions = [];
//         $this->valuesToSelect = $valuesToSelect;
//         $this->limits = "";
//         $this->orderBy = "";
//         $this->orderTypes = (object)[];
//         $this->orderTypes->ASC = "ASC";
//         $this->orderTypes->DESC = "DESC";
//         $this->orderTypes->RAND = "RAND()";
//     }

//     public function setTableName($tableName)
//     {
//         $this->tableName = $tableName;
//     }

//     public function setValuesToSelect($valuesToSelect)
//     {
//         $this->valuesToSelect = $valuesToSelect;
//     }

//     public function setLimits($quantity=1)
//     {
//         $this->limits = "LIMIT $quantity";
//     }

//     public function sortBy($type="ASC", $basedOnColumn = ""){
//         if ($type == $this->orderTypes->RAND)
//             $basedOnColumn = "";
//         else
//             $basedOnColumn .= " ".$basedOnColumn;

//         $this->orderBy = "ORDER BY$basedOnColumn $type";
//     }


//     public function addCondition()
//     {

//     }


// }


/**
 * Add Primary Keys to Table
 */
class sqlPrimaryKey
{
    private $tableName;
    private $keys;
    /**
     * @param string $tableName Name of the Table
     */
    function __construct($tableName = "")
    {
        $this->tableName = $tableName;
        $this->keys = [];
    }

    /**
     * Add column names to me made as a Primary Key
     * @param string $columnName Name of the column
     */
    public function addPrimaryKey($columnName)
    {
        array_push($this->keys, $columnName);
    }

    // public function deletePrimaryKey()
    // {
    //     if (trim($this->tableName) == "")
    //         return false;
    //     $sqlCommand = "ALTER TABLE `".$this->tableName."` DROP PRIMARY KEY";

    //     return executeQuery($sqlCommand);
    // }

    /**
     * Set Primary Keys for this table
     * @return bool Returns true if the PRIMARY KEY(s) are set. Otherwise returns false.
     */
    public function setKeys()
    {
        if (trim($this->tableName) == "")
            return false;

        if (count($this->keys) <= 0)
            return false;

        $sqlCommand = "ALTER TABLE `" . $this->tableName . "` DROP PRIMARY KEY, ADD PRIMARY KEY(";

        $temp = "";
        foreach ($this->keys as $k) {
            if (trim($temp) != "")
                $temp .= ", ";

            $temp .= "`$k`";
        }

        $sqlCommand = $sqlCommand . $temp . ")";

        return executeQuery($sqlCommand);
    }
}

/**
 * Add Unique Key to the table
 */
class sqlUniqueKey
{
    private $tableName;
    private $keys;

    /**
     * @param string $tableName Name of the Table
     */
    function __construct($tableName = "")
    {
        $this->tableName = $tableName;
        $this->keys = [];
    }


    /**
     * Add column names to me made as a Unique Key
     * @param string $columnName Name of the column
     */
    public function addUniqueKey($columnName)
    {
        array_push($this->keys, $columnName);
    }

    /**
     * Set Unique Keys for this table
     * @return bool Returns true if the UNIQUE KEY(s) are set. Otherwise returns false.
     */
    public function setKeys()
    {
        if (trim($this->tableName) == "")
            return false;

        if (count($this->keys) <= 0)
            return false;

        $sqlCommand = "ALTER TABLE `" . $this->tableName . "` ADD UNIQUE(";

        $temp = "";
        foreach ($this->keys as $k) {
            if (trim($temp) != "")
                $temp .= ", ";

            $temp .= "`$k`";
        }

        $sqlCommand = $sqlCommand . $temp . ")";

        return executeQuery($sqlCommand);
    }
}

/**
 * Show or Display Errors/Warnings in PHP
 * @param bool $showErrors Set True if errors should be shown. Otherwise Set false.
 */
function setPHPErrors($showErrors)
{
    if ($showErrors) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', 0);
        error_reporting(0);
    }
}

/**
 * Check if the Table exists in the database
 * @param string $tableName Name of the Table
 * @return bool Returns true if the Table exists in the database. Otherwise returns false.
 */
function isTablePresent($tableName)
{
    return fetchData("SELECT * FROM information_schema.tables WHERE table_schema = '".DB_NAME."' AND table_name = '$tableName' LIMIT 1") == null ? false : true;
}


/**
 * Upload a File and Save it
 */
class uploadFile
{
    private $directory;
    private $fileName;
    private $maxSize;
    private $fileFormats;
    private $compressImage;
    private $compressQuality;
    private $replaceFile;
    private $keepOriginal;
    private $compressedImageDirectory;

    /**
     * Upload a File and Save it
     */
    function __construct()
    {
        $this->directory = "./";
        // $this->fileName = "image_" . rand(1, 99999);
        $this->fileName = "";
        $this->maxSize = 0;
        $this->fileFormats = array();
        $this->compressImage = false;
        $this->compressQuality = 60;
        $this->replaceFile = false;
        $this->keepOriginal = true;
        $this->compressedImageDirectory = "./compressed/";
    }
    /**
     * set Directory where the file should be uploaded
     * @param string $directory Relative Directory where the file should be uploaded
     */
    public function setDirectory($directory)
    {
        if (empty(trim($directory))) {
            $this->directory = "./";
            $this->compressedImageDirectory = "./compressed/";
        } else {
            $this->directory = $directory;
            $this->compressedImageDirectory = $directory . "compressed/";
        }
    }


    /**
     * Set File Names
     * @param string $fileName Name of the file to be uploaded
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }


    /**
     * Set Maximum Size of the File to be uploaded (in MB)
     * @param int $Maximum Size of the File (in MB) to be uploaded. Set 0 for unlimited size.
     */
    public function setMaxSize($maxSize)
    {
        if ($maxSize > 0)
            $this->maxSize = $maxSize * 10485760;
        else
            $this->maxSize = 0;
    }

    /**
     * The File Formats to be accepted for Upload
     * @param array $fileFormats List of file formats to be accepted for Upload. Set Empty array for all File formats.
     */
    public function setFileFormats($fileFormats)
    {
        $this->fileFormats = $fileFormats;
    }


    /**
     * Whether the File should be replaced incase a file already exists with the same name.
     * @param boolean $replaceFile Set True to replace the existing file. Otherwise set False.
     */
    public function setReplaceFile($replaceFile)
    {
        $this->replaceFile = $replaceFile;
    }


    /**
     * Find the Extension of the File uploaded.
     * @param array $file The File Array ($_FILES['image'])
     * @return string The file extension without dot (.)
     */
    public function getFileExtension($file)
    {
        return strtolower(pathinfo($this->directory . basename($file["name"]), PATHINFO_EXTENSION));
    }


    /**
     * Whether the File should be compressed to a lower file size. (Only Works for Images)
     * @param boolean $compressImage Set True to compress the image. Otherwise set False.
     * @param int $compressQuality Set the compression quality of the image. Default 60. [Min 0 - Max 100]
     * @param boolean $keepOriginal Set True to Keep the original file + Compressed File. Otherwise set False to save only Compressed File.
     */
    public function compressImage($compressImage = true, $compressQuality = 60, $keepOriginal = true)
    {
        $this->compressImage = $compressImage;
        if ($compressQuality > 0 && $compressQuality < 100)
            $this->compressQuality = $compressQuality;

        $this->keepOriginal = $keepOriginal;
    }



    /**
     * Get The Name of The File Saved
     * @return string The File Name with extension.
     */
    public function getFileName()
    {
        return $this->fileName;
    }


    /**
     * Get the Directory where the File is stored.
     * @return string The Directory where the File is stored.
     */
    public function getFileDirectory()
    {
        return $this->directory;
    }


    /**
     * Get the Directory where the Compressed Image is stored.
     * @return string The Directory where the Compressed Image is stored.
     */
    public function getCompressedImageDirectory()
    {
        return $this->compressedImageDirectory;
    }

    /**
     * Get the Relative File Location where the File is stored.
     * @return string TheRelative File Location where the File is stored.
     */
    public function getFileLink()
    {
        return $this->getFileDirectory() . $this->getFileName();
    }


    /**
     * Get the Relative File Location where the Compressed Image is stored.
     * @return string TheRelative File Location where the Compressed Image is stored.
     */
    public function getCompressedImageLink()
    {
        if ($this->compressImage)
            if ($this->keepOriginal)
                return $this->getFileLink();
            else
                return $this->getCompressedImageDirectory() . $this->getFileName();
        else
            return "";
    }

     /**
     * Get the Relative File Directory where the Compressed Image is stored.
     * @return string The Relative File Directory where the Compressed Image is stored.
     */
    public function setCompressedImageDirectory($compressedImageDirectory)
    {
        if (empty(trim($compressedImageDirectory)))
            $this->compressedImageDirectory = $this->directory . "compressed/";
        else
            $this->compressedImageDirectory = $compressedImageDirectory;
    }


     /**
     * Upload the File
     * @param array $file The File to be uploaded.
     * @param string $target_file Target Destination for the File to be uploaded.
     * @return boolean Returns True if the file was uploaded successfully. Otherwise Returns false.
     */
    private function upload($file, $target_file)
    {

        if (!file_exists($this->directory))
            mkdir($this->directory);

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            if ($this->compressImage) {
                $info = getimagesize($target_file);


                if ($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg')
                    $image = imagecreatefromjpeg($target_file);

                elseif ($info['mime'] == 'image/gif')
                    $image = imagecreatefromgif($target_file);

                elseif ($info['mime'] == 'image/png')
                    $image = imagecreatefrompng($target_file);

                elseif ($info['mime'] == 'image/webp')
                    $image = imagecreatefromwebp($target_file);

                elseif ($info['mime'] == 'image/bmp')
                    $image = imagecreatefrombmp($target_file);
                else
                    return false;


                if (!$this->keepOriginal) {
                    $destination = $target_file;
                } else {
                    $destination = $this->compressedImageDirectory . $this->getFileName();
                    if (!file_exists($this->compressedImageDirectory))
                        mkdir($this->compressedImageDirectory);
                }

                imagejpeg($image, $destination, $this->compressQuality);
            }
            return true;
        } else
            return false;
    }

/**
     * Upload the File
     * @param array $file The File to be uploaded.
     * @param boolean $return_error_message Set True to get Operation Statement. Otherwise set False to get boolean value.
     * @return string|boolean Returns true on success. Otherwise returns false. Returns Operation Statement if $return_error_message is set to true.
     */
    public function uploadFile($file, $return_error_message = false)
    {
        if ($this->maxSize > 0 && $file["size"] >  $this->maxSize) {
            if ($return_error_message)
                return "File Too Large.";
            else
                return false;
        }

        if (empty(trim($this->fileName)))
            $this->fileName = $file["name"];


        $imageFileType = $this->getFileExtension($file);
        if (count($this->fileFormats) > 0) {
            $temp = false;
            foreach ($this->fileFormats as $format) {
                if ($imageFileType == $format) {
                    $temp = true;
                    break;
                }
            }

            if (!$temp) {
                if ($return_error_message)
                    return "Unsupported File Format Provided";
                else
                    return false;
            }
        }

        $target_file = $this->directory . urlencode($this->fileName);
        // $uploadPath = $siteLink . "wallpapers/image/" . urlencode($this->imageName);
        if ($this->replaceFile) {
            if (file_exists($target_file)) {
                unlink($target_file);
            }
        } else if (file_exists($target_file)) {
            if ($return_error_message)
                return "File Already Exists";
            else
                return false;
        }

        $check = getimagesize($file["tmp_name"]);
        if ($check !== false) {
        } else {
            if ($return_error_message)
                return "Invalid File Provided";
            else
                return false;
        }


        if ($this->upload($file, $target_file)) {
            if ($return_error_message)
                return "File Uploaded successfully";
            else
                return true;
        } else {
            if ($return_error_message)
                echo "Sorry, there was an error uploading the file";
            else
                return false;
        }
    }
}
