<?php
include_once('model/Faq.php');
class controller_Faq
{
  function CreateFaq()
  {
    isAdmin();
    global $request;
    if($request['name'] && $request['body'] && $request['type'])
    {
      $result=model_faq::CreateFaq($request);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'FAQ added'));
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
    if($request['id'] && ($request['name'] || $request['body'] || $request['type']))
    {
      $result=model_faq::UpdateFaq($request);
      if($result>0)
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
    global $request,$CurrentUser;
    if($CurrentUser->access=="Jobseeker")
    {
      $request['type']=2;
    }

    if($CurrentUser->access=="employer")
    {
      $request['type']=1;
    }

      $result=model_faq::GetFaq($request);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no data'));
      }
    }



}
 ?>
