<?php
global $dbconfig, $ServerRequestLog, $ServerResponseLog, $ServerQuaryLog, $ErrorLog, $request, $db, $ServerURL, $JWTKey, $JWTExpirationTime, $SecretKey, $Recaptcha, $BeforePunchIn, $MinimumWorkingHours;

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

// JWT token Expiration Time
$JWTExpireTime=86400; // 1 days
$JWTKey='MIICXQIBAAKBgQCqtM24cU6wcO6wn0jk2S3KNyWJzXurkMzmCnvDxLZ5H5wzR/Yx'; // key for JWT

// google recapcha
//$SiteKey="6Lc2L6IUAAAAAG0I8PuARFQibZcDRuU9vM8NPrG1";
$SecretKey="6Lc2L6IUAAAAALMeBGsnTHy12WC_maY82MJ4nkiE";
$Recaptcha='ON'; // ON / OFF


// set allow to jobseeker to bunch befor clock_in time (min)
$BeforePunchIn=15;
// set minimum punch out time
$MinimumWorkingHours=15;

$ServerURL="http://127.0.0.1/ooget/";
error_reporting(E_ALL);
ini_set('memory_limit','51200M');
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('post_max_size', '250M');

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
//error_reporting(0);

?>
