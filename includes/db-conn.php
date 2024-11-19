<?php
$sname = 'localhost';
$uname= 'root';
$password = "";
$db_name = "db_university";

$conn = mysqli_connect($sname, $uname, $password, $db_name);

    if(!$conn){
        echo "Connection Failed";
    }else{
        "Connection Success!";
    }
?>


