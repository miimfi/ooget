<?php
include_once('model/Bank.php');
class controller_Bank
{
  function CreateBank()
  {
    isAdmin();
    global $request;
    if($request['full_name'] && $request['short_name']  && $request['bank_code'] && $request['hint'])
    {
      $result=model_bank::CreateBank($request);
      if($result)
      {

        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'created'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
    }

  }

  function DeleteBank()
  {
    isAdmin();
    global $request;
    if($request['id'])
    {
      $result=model_bank::DeleteBank($request['id']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'deleted'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
    }

  }

  function GetBankList()
  {
    global $request;
    //( $request['from'] && $request['to'] )
      $result=model_bank::GetBankList();
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no bank details'));
      }
  }

  function UpdateBankDetails()
  {
    isAdmin();
    global $request;
    if($request['id'])
    {
      $result=model_bank::UpdateBankDetails($request);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'updated'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'error'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
    }


  }

  function GetBankDetails()
  {
    global $request;
    if($request['id'])
    {
      $result=model_bank::GetBankDetails($request['id']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no data'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
    }

  }

}
 ?>
 
