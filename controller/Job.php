<?php
include_once('model/Job.php');
class controller_Job
{
  function CreateJob()
  {
    /* job status
      1 job pending (waiting for admin accept)
      2 live job
      3 closed
    */
    global $request, $CurrentUser;
    if($CurrentUser->access!="admin")
    {
        $request['employer_id']=$CurrentUser->companyid;
        $request['status']=1;
    }
    else {
      $request['status']=2;
    }

    foreach ($request as $key => $value) {
      if($value='' )
      echo $key;
    }
    //echo $request['job_name'] ." # ". $request['department'] ." # ". $request['employement_type'] ." # ". $request['description'] ." # ". $request['specializations'] ." # ". $request['working_environment'] ." # ". $request['pax_total'] ." # ". $request['grace_period'] ." # ". $request['over_time_rounding'] ." # ". $request['over_time_minimum'] ." # ". $request['from'] ." # ". $request['to'] ." # ". $request['start_time'] ." # ". $request['end_time'] ." # ". $request['work_days_type'] ." # ". $request['postal_code'] ." # ". $request['address'] ." # ". $request['unit_no'] ." # ". $request['region'] ." # ". $request['employer_id'] ." # ". $request['location'] ." # ". $request['charge_rate'] ." # ". $request['markup_rate'] ." # ". $request['markup_in'] ." # ". $request['jobseeker_salary'] ." # ". $request['markup_amount'] ." # ". $request['project_name'];
    if($request['job_name'] && $request['department'] && $request['employement_type'] && $request['description'] && $request['specializations'] && $request['working_environment'] && $request['pax_total'] && $request['grace_period'] && $request['over_time_rounding'] && $request['over_time_minimum'] && $request['from'] && $request['to'] && $request['start_time'] && $request['end_time'] && $request['work_days_type'] && $request['postal_code'] && $request['address'] && $request['unit_no'] && $request['region'] && $request['employer_id'] && $request['location'] && $request['project_name'])
    {
      $result=model_Job::CreateJob($request);
      if($result>0)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Job Created'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
      }
    }
    else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Please fill all mandatory fields'));
    }
  }

//  pendingjob, alljob, livejob, openjob, closedjob, JobapliedList (based on job)
//appliedjob(appliedjob, offered), savedjob, matchedjob
  function GetAllJobList()
  {
      // view all jobs (pending, live, open, closed)
      global $request, $CurrentUser;
      if($CurrentUser->access!="admin")
      {
          $request['employerid']=$CurrentUser->companyid;
      }
      $result=model_Job::GetAllJobList($request['employerid']);
      if(is_array($result))
      {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Job list empty'));
      }
  }

  function GetOpenJobList()
  {
      global $request, $CurrentUser;
      if($CurrentUser->access!="admin" && $CurrentUser->access!="jobseeker")
      {
          $request['employerid']=$CurrentUser->companyid;
      }
      $result=model_Job::GetOpenJobList($request['employerid']);
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Job'));
      }
  }

  function GetLiveJobList()
  {
    global $request, $CurrentUser;
    if($CurrentUser->access!="admin" && $CurrentUser->access!="jobseeker")
    {
        $request['employerid']=$CurrentUser->companyid;
    }
    $result=model_Job::GetLiveJobList($request['employerid']);
    if(is_array($result))
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Job'));
    }
  }

  function GetClosedJobList()
  {
    global $request, $CurrentUser;
    if($CurrentUser->access!="admin")
    {
        $request['employerid']=$CurrentUser->companyid;
    }
    $result=model_Job::GetClosedJobList($request['employerid']);
    if(is_array($result))
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Job'));
    }
  }

  function GetPendingJobList()
  {
    global $request, $CurrentUser;
    if($CurrentUser->access!="admin")
    {
        $request['employerid']=$CurrentUser->companyid;
    }
    $result=model_Job::GetPendingJobList($request['employerid']);
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


  function ChangeStatus()
  {

    global $request;
    if($request['jobid'] && $request['status']>0)
    {
      $result=model_Job::ChangeStatus($request['jobid'], $request['status']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Job id not found / no change'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid jobseeker id or status'));
    }
  }

  function JobApply()
  {
    global $request, $CurrentUser;
    if($CurrentUser->access=='Jobseeker' && $request['jobid'])
    {
      $result=model_Job::JobApply($CurrentUser->id,$request['jobid']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'job applide'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Job id not found / job closed / Already Applied'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid job id / invalide jobseeker account'));
    }
  }

  function JobOffered()
  {
    global $request, $CurrentUser;
    if($request['job_Applied_id'])
    {
      $result=model_Job::JobOffered($CurrentUser->id,$request['job_Applied_id']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Job Offered'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'job not found'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid id / Already offered'));
    }
  }

  function ApplideReject()
  {
    global $request, $CurrentUser;
    if($request['job_Applied_id'])
    {
      $result=model_Job::ApplideReject($CurrentUser->id,$request['job_Applied_id']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Application Rejected'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'error'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid Application'));
    }
  }

  function JobseekerJobAccept()
  {
    global $request, $CurrentUser;
    if($request['job_Applied_id'] && $CurrentUser->access=="Jobseeker")
    {
      $result=model_Job::JobAccept($CurrentUser->id,$request['job_Applied_id']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Job Accepted'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'error'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid ID'));
    }
  }


  function GetAppliedList()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=="jobseeker")
    {
      $request['jobseekerid']=$CurrentUser->id;
    }
    $result=model_Job::GetAppliedList($request['jobseekerid'],$request['jobid']);
    if(is_array($result))
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Job'));
    }
  }


}
