<?php
include_once('model/Timesheet.php');
class controller_Timesheet
{

  function GetJobseekerTimeSheet()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=="Jobseeker")
    {
      $request['jobseekerid']=$CurrentUser->id;
    }
    if($request['jobseekerid'] && $request['from'] && $request['to'])
    {
      $result=model_timesheet::GetJobseekerTimeSheet($request['jobseekerid'],$request['from'],$request['to']);
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no timesheet'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Check input'));
    }
  }

    function GetTodayJobseekerTimeSheet()
    {
      global $request,$CurrentUser;
      if($CurrentUser->access=="Jobseeker" && $request['contract_id'])
      {
        $request['jobseekerid']=$CurrentUser->id;
        $result=model_timesheet::GetTodayJobseekerTimeSheet($request['jobseekerid'],$request['contract_id']);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no timesheet found'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Check input'));
      }
    }

    function PunchIn()
    {
      global $request,$CurrentUser;
      if($CurrentUser->access=="Jobseeker" && $request['timesheet_id'])
      {
        $request['jobseekerid']=$CurrentUser->id;
        $result=model_timesheet::PunchIn($request['jobseekerid'],$request['timesheet_id']);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>"You can't punch in this timesheet"));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'invalide timesheet id / invalid jobseeker account'));
      }
    }

    function VerifiedPunchIn()
    {
      global $request,$CurrentUser;
      if($request['in_time'] && $request['timesheet_id'])
      {
        $userid=$CurrentUser->id;
        $result=model_timesheet::VerifiedPunchIn($request['in_time'],$request['timesheet_id'],$userid);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>"Error / timesheet not found!"));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'invalide timesheet id / Clock In time'));
      }
    }

    function PunchOut()
    {
      global $request,$CurrentUser;
      if($CurrentUser->access=="Jobseeker" && $request['timesheet_id'])
      {
        $request['jobseekerid']=$CurrentUser->id;
        $result=model_timesheet::PunchOut($request['jobseekerid'],$request['timesheet_id']);
        if($result)
        {
          if($result['code']=='success')
          {
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
          }
          else {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>$result));
          }

        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>"You can't punch out this timesheet"));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'invalide timesheet id / invalid jobseeker account'));
      }
    }

    function VerifiedPunchOut()
    {
      global $request,$CurrentUser;
      if($request['out_time'] && $request['timesheet_id'])
      {
        $userid=$CurrentUser->id;
        $result=model_timesheet::VerifiedPunchOut($request['out_time'],$request['timesheet_id'],$userid);
        if($result)
        {
          if($result['code']=='success')
          {
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
          }
          else {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>$result));
          }

        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>"Error "));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'invalide timesheet id / out time'));
      }
    }




}
?>
