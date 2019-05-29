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

  function SaveJob()
  {
    global $request, $CurrentUser;
    if($CurrentUser->access=='Jobseeker' && $request['jobid'])
    {
      $result=model_Job::SaveJob($request['jobid'], $CurrentUser->id);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Job saved'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'error / alredy saved'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'invalid jobseeker id / job id'));
    }
  }

  function RemoveSavedJob()
  {
    global $request, $CurrentUser;
    if($CurrentUser->access=='Jobseeker' && $request['jobid'])
    {
      $result=model_Job::RemoveSavedJob($request['jobid'], $CurrentUser->id);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Job Removed'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'error'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'invalid jobseeker id / job id'));
    }
  }

  function GetOpenJobList()
  {
      global $request, $CurrentUser;
      if($CurrentUser->access!="admin" && $CurrentUser->access!="Jobseeker")
      {
          $request['employerid']=$CurrentUser->companyid;
      }

      if($CurrentUser->access=="Jobseeker")
      {
        $saved_job_jobseeker_id=$CurrentUser->id;
      }
      else {
        $saved_job=0;
      }
      $result=model_Job::GetOpenJobList($request['employerid'],$saved_job_jobseeker_id);
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
    if($CurrentUser->access!="admin" && $CurrentUser->access!="Jobseeker")
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
    global $request, $CurrentUser;
    if($CurrentUser->access=="Jobseeker")
    {
      $JobseekerId=$CurrentUser->id;
    }
    if($request['jobid']>0)
    {
      $result=model_Job::GetJobDetails($request['jobid'],$JobseekerId);
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
    if($request['contracts_id'])
    {
      $result=model_Job::JobOffered($CurrentUser->id,$request['contracts_id']);
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
    if($request['contracts_id'])
    {
      $result=model_Job::ApplideReject($CurrentUser->id,$request['contracts_id']);
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
    if($request['contract_id'] && $CurrentUser->access=="Jobseeker")
    {
      $result=model_Job::JobAccept($CurrentUser->id,$request['contract_id']);
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
    if($CurrentUser->access=="Jobseeker")
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

  function GetJobseekerContractList()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=="Jobseeker")
    {
      $result=model_Job::GetJobseekerContractList($CurrentUser->id);
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Contracts'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'only for jobseekers'));
    }
  }

  function GetJobseekerTimeSheet()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=="Jobseeker")
    {
      $request['jobseekerid']=$CurrentUser->id;
    }
    if($request['jobseekerid'] && $request['from'] && $request['to'])
    {
      $result=model_Job::GetJobseekerTimeSheet($request['jobseekerid'],$request['from'],$request['to']);
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
      $result=model_Job::GetTodayJobseekerTimeSheet($request['jobseekerid'],$request['contract_id']);
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
      $result=model_Job::PunchIn($request['jobseekerid'],$request['timesheet_id']);
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

  function PunchOut()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=="Jobseeker" && $request['timesheet_id'])
    {
      $request['jobseekerid']=$CurrentUser->id;
      $result=model_Job::PunchOut($request['jobseekerid'],$request['timesheet_id']);
      if($result)
      {
        if($result['code']=='succes')
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

}
