<?php
define('TIMEZONE', 'America/New_York');
define("DEVELOPING", true);
define("DATA_FOLDER", "data_cms");
define("CMS_PATH", str_replace("app",DATA_FOLDER,dirname($_SERVER['SCRIPT_NAME'])));

define("FOR_XML", '../'.DATA_FOLDER);
define("BASE_PATH", dirname($_SERVER['SCRIPT_NAME']));
define("ORIGINALS_FOLDER", '/originals/');
//define("ORIGINALS_FOLDER", '/');


if (!DEVELOPING) {
    ini_set('display_errors', 0);error_reporting(0);
} else {
    ini_set('display_errors', true);error_reporting(E_ALL ^ E_NOTICE);
}

$vimeo = 1;
?>