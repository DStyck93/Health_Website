<?php
ob_start(); // output buffering
session_start();

// Assign file paths to PHP constants
define("PRIVATE_PATH", dirname(__FILE__));
define("PROJECT_PATH", dirname(PRIVATE_PATH));
const PUBLIC_PATH = PROJECT_PATH . '/public';
const SHARED_PATH = PRIVATE_PATH . '/shared';

// Assign the root URL to a PHP constant
$public_end = strpos($_SERVER['SCRIPT_NAME'], '/public') + 7;
$doc_root = substr($_SERVER['SCRIPT_NAME'], 0, $public_end);
define("WWW_ROOT", $doc_root);

require_once('functions.php');
require_once('database.php');
require_once('query_functions.php');
require_once('validation_functions.php');
require_once('auth_functions.php');

$db = db_connect();
//$db -> change_user(DB_USER, DB_USER_PASS, DB_NAME);
$errors = [];
?>