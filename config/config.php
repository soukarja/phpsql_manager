<?php

//check if HTTP HOST starts with localhost
    
if (strpos($_SERVER['HTTP_HOST'], 'localhost') === 0) {
    //It's a local server
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'test');

    setPHPErrors(true);
    
} else {
    //It's a remote server
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'test');

    setPHPErrors(false);
}

 
/* Attempt to connect to MySQL database */
// $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
function connectSQL(){   
    $link =  mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    if($link === false){
        die("ERROR: Could not connect. " . mysqli_connect_error());
    }
    return $link;
}
// $link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
// Check connection

?>