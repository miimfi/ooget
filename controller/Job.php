<?php
include_once('model/Job.php');
class controller_Job
{
  function CreateJob()
  {
    global $request;
    if($request['job_name'] && $request['department'] && $request['employement_type'] && $request['description'] && $request['specializations'] && $request['working_environment'] && $request['pax'] && $request['grace_period'] && $request['over_time_rounding'] && $request['over_time_start_from'] && $request['from'] && $request['to'] && $request['start_time'] && $request['end_time'] && $request['work_days_type'] && $request['postal_code'] && $request['address'] && $request['unit_no'] && $request['region'] && $request['location'] && $request['charge_rate'] && $request['markup_rate'] && $request['markup_in'] && $request['jobseeker_salary'] && $request['markup_amount'] && $request['auto_accepted'] && $request['project_name'])
    {
      $result=model_Job::CreateJob($request);
      if($result>0)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Jpb Created'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
      }
    }
    else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Please fill all mandatory fields'));
    }
  }

  function GetJobList()
  {
      global $request;
      $result=model_Job::GetJobList($request['employerid']);
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Job'));
      }
  }

  function GetJobDetails()
  {
    global $request;
    if($request['jobid']>0)
    {
      $result=model_Job::GetJobDetails($request['jobid']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Job id not found'));
      }
    }else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
    }

  }

}
