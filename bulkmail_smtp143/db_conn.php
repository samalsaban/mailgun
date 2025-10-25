<?php

$sname= "localhost";

$unmae= "triunity";

$password = "Pagal@123";

$db_name = "triunity";

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {

    echo "Connection failed!";

}
?>