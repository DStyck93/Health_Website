<?php

require_once('db_credentials.php');

function db_connect() {
    try {
        // TODO -- Switch to AWS database before pushing
        return mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
//        return mysqli_connect(DB_SERVER, DB_ADMIN, DB_ADMIN_PASS, DB_NAME);
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