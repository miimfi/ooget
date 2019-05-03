<?php
include_once('model/Employer.php');
use \Firebase\JWT\JWT;
class controller_Employer
{
  function CreateEmployer()
  {

    if($request['name'] && $request['profile'] && $request['uen'] && $request['companycode'] && $request['industry'] && $request['country'])
    {
      $CheckCompanyCodeExist=CheckCompanyCodeExist($request['companycode']);
      if(!$CheckCompanyCodeExist)
      {
        $result=model_Employer::CreateEmployer();
        if($result>0)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'User Created'));
        }
        else {
          lib_ApiResult::JsonEncode('error');
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Company code already exist'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Please fill all mandatory fields'));
    }

  }
}
