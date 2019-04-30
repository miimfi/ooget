<?php
global $dbconfig, $ServerRequestLog, $ServerResponseLog, $ServerQuaryLog, $ErrorLog, $request, $db, $ServerURL;

// DB Config
$dbconfig['db_server'] = '127.0.0.1';
$dbconfig['db_port'] = '3306';
$dbconfig['db_username'] = 'doss';
$dbconfig['db_password'] = 'rootroot';
$dbconfig['db_name'] = 'ooget';
$dbconfig['db_hostname'] = $dbconfig['db_server'].':'.$dbconfig['db_port'];

// log file 0=off/1=on file path : log/
$ServerRequestLog=0;
$ServerResponseLog=0;
$ServerQuaryLog=0;
$ServerErrorLog=0;

$ServerURL="http://127.0.0.1/ooget/";
error_reporting(E_ALL);

ini_set('memory_limit','51200M');
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('post_max_size', '250M');

 ?>
