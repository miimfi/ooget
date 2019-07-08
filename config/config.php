<?php
global $dbconfig, $ServerRequestLog, $ServerResponseLog, $ServerQuaryLog, $ErrorLog, $request, $db, $ServerURL, $JWTKey, $JWTExpirationTime, $SecretKey, $Recaptcha,$website_Name;

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
$Recaptcha='OFF'; // ON / OFF


//mail server
global $MailConf_from,$MailConf_Host,$MailConf_SMTPAuth,$MailConf_Username,$MailConf_Password,$MailConf_SMTPSecure,$MailConf_Port,$MailConf_SMTPDebug,$ForgotPasswordReturnURL;
$MailConf_from=array('id'=>'sqtesting2016@gmail.com' ,'name'=> 'OogetSupport' );
$MailConf_Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers
$MailConf_SMTPAuth   = true;                                   // Enable SMTP authentication
$MailConf_Username   = 'sqtesting2016@gmail.com';                     // SMTP username
$MailConf_Password   = 'P@$$word@9999';                               // SMTP password
$MailConf_SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
$MailConf_Port       = 465;
$MailConf_SMTPDebug  = 0;  // error return 0-2
$ForgotPasswordReturnURL="http://127.0.0.1/keyreturn.php";


$ServerURL="http://127.0.0.1/ooget/";
$website_Name="Ooget";
error_reporting(E_ALL);
ini_set('memory_limit','51200M');
set_time_limit(0);
ini_set('max_execution_time', 0);
ini_set('post_max_size', '250M');

ini_set('display_errors', 'Off');
ini_set('display_startup_errors', 'Off');
//error_reporting(0);

?>
