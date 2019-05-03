<?php
include('config/config.php');
include('lib/DB.php');
include('lib/ApiResult.php');
include('lib/IoLib.php');
include('lib/jwt.php');
include('controller/router.php');





//lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$request));
//print_r($_SERVER['HTTP_TOKEN']);
/*if (isset($_SERVER['REQUEST_URI']))
{
    $params = explode("/", ltrim($_SERVER['REQUEST_URI'], "/"));
    print_r($params);
}*/

 ?>
