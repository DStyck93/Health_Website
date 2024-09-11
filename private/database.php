<?php

require_once('db_credentials.php');

function db_connect() {
    try {
        return mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    } catch (Exception) {
        exit('Could not connect to database.');
    }
}

function db_disconnect($connection): void {
    if(isset($connection)) {
        mysqli_close($connection);
    }
}
?>