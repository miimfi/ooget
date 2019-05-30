<?php
include_once('model/Terms.php');
class controller_Terms
{
  function CreateTerms()
  {
    isAdmin();
    global $request;
    if($request['ref_id'] && $request['title'] && $request['body'] && $request['type'])
    {
      $result=model_terms::CreateTerms($request);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
    }

  }

  function DeleteTerms()
  {
    isAdmin();
    global $request;
    if($request['id']>0)
    {
      $result=model_terms::DeleteTerms($request['id']);
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


  function UpdateTerms()
  {
    isAdmin();
    global $request;
    if($request['id'] && $request['title'] && $request['body'])
    {
      $result=model_terms::UpdateTerms($request);
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

  function GetTerms()
  {
    global $request, $CurrentUser;
    if($CurrentUser->access=='Jobseeker')
    {
      $request['type']=2;
      $request['ref_id']=$request['job_id'];
      $request['id']=null;
    }

    if($CurrentUser->access=='employer')
    {
      $request['type']=1;
      $request['ref_id']=$CurrentUser->id;
      $request['id']=null;
    }


    if($request['id'] || $request['type'])
    {
      $result=model_terms::GetTerms($request['id'],$request['ref_id'],$request['type']);
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

  function GetTermsList()
  {
    isAdmin();
    global $request;
      $result=model_terms::GetTermsList($request['type']);
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Terms list'));
      }
  }

}
 ?>
