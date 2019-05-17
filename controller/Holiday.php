<?php
include_once('model/Holiday.php');
class controller_Holiday
{
  function CreateHoliday()
  {
    isAdmin();
    global $request;
    if($request['name'] && $request['date'])
    {
      $result=model_holiday::CreateHoliday($request['name'],$request['date']);
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

  function DeleteHoliday()
  {
    isAdmin();
    global $request;
    if($request['id'])
    {
      $result=model_holiday::DeleteHoliday($request['id']);
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

  function GetHolidayList()
  {
    global $request;
    //( $request['from'] && $request['to'] )
      $result=model_holiday::GetHolidayList($request);
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no holidays'));
      }
  }

  function UpdateHoliday()
  {
    isAdmin();
    global $request;
    if($request['id'])
    {
      $result=model_holiday::UpdateHoliday($request);
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

  function GetHoliday()
  {
    global $request;
    if($request['id'] || $request['date'] || $request['name'])
    {
      $result=model_holiday::GetHoliday($request);
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
