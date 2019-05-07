<?php
include("lib/recaptcha.php");

class controller_Jobseekers
{

  function CreateJobseeker()
  {
    global $request;
    $ClientKey=$request['g-recaptcha-response'];
    $Recaptcha=recaptcha($ClientKey);
    if($Recaptcha=='success')
    {lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'success'));}
    else {lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'recaptcha failed'));}
  }

}
