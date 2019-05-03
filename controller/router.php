<?php
// Auto controller
// controller file automaticaly include based on REQUEST params ("?module=<modulename>")
// get posted data
include('controller/Users.php');
$AuthResult=controller_Users::Authentication();

if($AuthResult || ($request['mode']=='Login' && $request['module']=='Users')){
  $Modules;
  if($request['module'])
  {
    $Modules="controller/".$request['module'].".php";
  }

  if (file_exists($Modules))
  {
          if($request['module']!='Users')
          {
            include $Modules;
          }

         $ClassName="controller_".$request['module'];
         if(class_exists($ClassName))
         {
           $FunctionName=($request['mode']?$request['mode']:'__construct');
           if(method_exists($ClassName, $FunctionName))
           {
             $ClassName::$FunctionName();
           }
           else {
               RequestNotFound();
           }
         }
         else {
            RequestNotFound();
         }
  }
  else {
    RequestNotFound();
  }
}
else {
  lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'login failed'));
  exit;

}


function RequestNotFound()
{
  global $request;
  $result=array('status'=>400,'result'=>'Request not found!','request'=>$request);
  lib_ApiResult::JsonEncode($result);
}
 ?>
