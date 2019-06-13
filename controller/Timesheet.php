<?php
include_once('model/Timesheet.php');
class controller_Timesheet
{

  function GetTimeSheet()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=="Jobseeker")
    {
      $request['jobseekerid']=$CurrentUser->id;
    }
    if ($CurrentUser->access=="employer") {
      $request['employerid']=$CurrentUser->id;
    }

    if($request['contractid'] || $request['timesheet_id'])
    {
      $result=model_timesheet::GetTimeSheet($request);
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>'no timesheet'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Check input'));
    }
  }

    function TimesheetSetNotes()
    {
      global $request,$CurrentUser;

      if($request['timesheetid'] && $request['notes'])
      {
        $result=model_timesheet::TimesheetSetNotes($request['timesheetid'],$request['notes'],$CurrentUser->id,$CurrentUser->companyid);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Notes added'));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Update error'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Check input'));
      }
    }

    function VerifiedTimesheet()
    {
      global $request,$CurrentUser;

      if($request['timesheetid'])
      {
        $result=model_timesheet::VerifiedTimesheet($request['timesheetid'],$CurrentUser->id,$CurrentUser->companyid);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Timesheet verified'));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'error'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Check timesheetid'));
      }
    }


    function GetJobTimesheetList()
    {
      // for job based time_sheet report
      global $request,$CurrentUser;
      if($request['jobid'] && $request['from'] && $request['to'])
      {
        $result=model_timesheet::GetJobTimesheetList($request['jobid'],$request['from'],$request['to'],$CurrentUser->companyid);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>'timesheet not found'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Check job id / from / to'));
      }
    }

    function GetJobContractTimesheetList()
    {
      // for job based time_sheet report
      global $request,$CurrentUser;
      if($request['jobid'] && $request['from'] && $request['to'])
      {
        $result=model_timesheet::GetJobContractTimesheetList($request['jobid'],$request['from'],$request['to'],$CurrentUser->companyid);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>'timesheet not found'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Check job id / from / to'));
      }
    }

    function GetEmployerContractTimesheetList()
    {
      // report for get jobseeker's all timesheet based on contracts
      global $request,$CurrentUser;
      if($CurrentUser->companyid)
      {
        $request['employerid']=$CurrentUser->companyid;
      }
      if($request['employerid'])
      {
        $result=model_timesheet::GetEmployerContractTimesheetList($request['employerid']);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>'timesheet not found'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'invalid input'));
      }
    }

    function GetEmployerContractTimesheetListCom()
    {
      // report for get jobseeker's all timesheet based on contracts
      global $request,$CurrentUser;
      if($CurrentUser->companyid)
      {
        $request['employerid']=$CurrentUser->companyid;
      }
      if($request['employerid'])
      {
        $result=model_timesheet::GetEmployerContractTimesheetListCom($request['employerid']);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>'timesheet not found'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'invalid input'));
      }
    }

    function GetJobseekerContractTimesheetList()
    {
      // report for get jobseeker's all timesheet based on contracts
      global $request,$CurrentUser;
      if($CurrentUser->access=="Jobseeker")
      {
        $request['jobseekerid']=$CurrentUser->id;
      }
      if($request['jobseekerid'] && $request['from'] && $request['to'])
      {
        $result=model_timesheet::GetJobseekerContractTimesheetList($request['jobseekerid'],$request['from'],$request['to'],$CurrentUser->companyid);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>'timesheet not found'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Check jobseeker id / from / to'));
      }
    }

    function JobseekerLateInfo()
    {
      global $request,$CurrentUser;

      if($request['timesheetid'] && $request['lateinfo'] && $CurrentUser->access=="Jobseeker")
      {
        $result=model_timesheet::JobseekerLateInfo($request['timesheetid'],$request['lateinfo'],$CurrentUser->id);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Notes added'));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Update error'));
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

    function TimesheetSetHoliday()
    {
      global $request,$CurrentUser;
      if(($request['status']=='Y' || $request['status']=='N' || $request['status']=='P') && $request['timesheet_id'])
      {
        $request['uid']=$CurrentUser->id;
        $result=model_timesheet::TimesheetSetHoliday($request);
        if($result)
        {
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'updated'));

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
