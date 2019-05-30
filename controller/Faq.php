<?php
include_once('model/Faq.php');
class controller_Faq
{
  function CreateFaq()
  {
    isAdmin();
    global $request;
    if($request['name'] && $request['date'])
    {
      $result=model_faq::CreateFaq($request['name'],$request['date']);
      if($result)
      {
        $result_data['id']=$result;
        $result_data['date']=$request['date'];
        $result_data['name']=$request['name'];
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result_data));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
    }

  }

  function DeleteFaq()
  {
    isAdmin();
    global $request;
    if($request['id'])
    {
      $result=model_faq::DeleteFaq($request['id']);
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


  function UpdateFaq()
  {
    isAdmin();
    global $request;
    if($request['id'])
    {
      $result=model_faq::UpdateFaq($request);
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

  function GetFaq()
  {
    global $request;
    if($request['id'] || $request['date'] || $request['name'])
    {
      $result=model_faq::GetFaq($request);
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
