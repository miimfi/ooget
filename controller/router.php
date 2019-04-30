<?php
// Auto controller
// controller file automaticaly include based on REQUEST params ("?module=<modulename>")

$Modules;
if($request['module'])
{
  $Modules="controller/".$request['module'].".php";
}

if (file_exists($Modules))
{
       include $Modules;
}
else {
  $result=array('status'=>400,'result'=>'Request not found!');
  lib_ApiResult::JsonEncode($result);
}
 ?>
