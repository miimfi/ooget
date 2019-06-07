<?php
// Auto controller
// controller file automaticaly include based on REQUEST params ("?module=<modulename>")
// get posted data
include('controller/Users.php');
$AuthResult=lib_jwt::Authentication();

if(!$request || $request['imgv'])
{
  //if(!$request && $AuthResult)
  $actual_link =$_SERVER[REQUEST_URI];
  $imgpath_tmp=explode("media",$actual_link);
  $RealName=explode("?",$imgpath_tmp[1]);
  if($RealName[0])
  {
    $imgpath='./media'.$RealName[0];
  if(file_exists($imgpath))
  {
    $im = file_get_contents($imgpath);
  }
  else {
    $im = file_get_contents('./media/nouser.png');
  }
  header("Content-type: image/jpeg");
  echo $im;
  die();
  }
}



function CheckJobseekerModuleAccess()
{
  global $request,$Jobseeker_Allow_Module;
  $input_request=array('module' => $request['module'],'mode'=>$request['mode']);
  if(in_array($input_request,$Jobseeker_Allow_Module))
  {
    return true;
  }
  else {
    return false;
  }
}


if($CurrentUser->access=='Jobseeker' && !CheckJobseekerModuleAccess())
{

  $request['module']='Jobseeker';
}

if($AuthResult || $request['mode']=='CreateEmployer' || $request['mode']=='Login' || $request['mode']=='CreateJobseeker' || $request['mode']=='CheckCompanyUenExist' || $request['mode']=='CheckEmail' || $request['mode']=='CheckUnique')
{
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

function isAdmin()
{
  global $CurrentUser;
  if($CurrentUser->access!='admin')
  {
    lib_ApiResult::JsonEncode(array('status'=>409,'result'=>'access denied'));
  }
}
 ?>
