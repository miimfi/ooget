<?php
include_once('model/Employer.php');
use \Firebase\JWT\JWT;
class controller_Employer
{
  function CreateEmployer()
  {
    global $request;
    if($request['name'] && $request['profile'] && $request['uen'] && $request['companycode'] && $request['industry'] && $request['country'])
    {
      $CheckCompanyExist=model_Employer::CheckCompanyExist($request['companycode'],$request['uen']);
      if(!$CheckCompanyExist)
      {
        $result=model_Employer::CreateEmployer();
        if($result>0)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Employer Created'));
        }
        else {
          lib_ApiResult::JsonEncode('error');
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Company code / UEN already exist'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Please fill all mandatory fields'));
    }

  }
  function UpdateEmployer()
  {
    global $request;
    if($request['employerid'])
    {
      $result=model_Employer::UpdateEmployer();
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Employer ID is empty'));
    }
  }

  function DeleteEmployer()
  {
    global $request;
    $isfound= model_Employer::GetEmployer($request['employerid']);
    if($request['employerid'] && $isfound)
    {
      $result=model_Employer::DeleteEmployer($request['employerid']);
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'deleted'));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Employer ID not found'));
    }
  }

  function GetEmployer()
  {
    global $request;
    if($request['employerid'])
    {
      $result=model_Employer::GetEmployer($request['employerid']);
    }
    else {
      $result=model_Employer::GetEmployer();
    }

    if($result)
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'No data'));
    }


  }
}
