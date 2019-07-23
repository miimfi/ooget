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
    if($request['job_name'] && $request['department'] && $request['employment_type'] && $request['description'] && $request['specializations'] && $request['working_environment'] && $request['pax_total'] && $request['grace_period'] && $request['over_time_rounding'] && $request['over_time_minimum'] && $request['from'] && $request['to'] && $request['start_time'] && $request['end_time'] && $request['work_days_type'] && $request['postal_code'] && $request['address'] && $request['unit_no'] && $request['region'] && $request['employer_id'] && $request['location'] && $request['project_name'])
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

  function UpdateJob()
  {
    global $request, $CurrentUser;
    if($request['jobid'])
    {
      $request['current_user']=$CurrentUser->id;
      if($CurrentUser->companyid>0)
      {
          $request['employer_id']=$CurrentUser->companyid;
      }
      $getJobDetails=model_Job::GetJobDetails($request['jobid']);
      if($getJobDetails['status']!=1)
      {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Job not pending'));
      }
      if(is_array($getJobDetails))
      {
        foreach ($getJobDetails as $key => $value) {
          if(!array_key_exists($key,$request))
          {
            $request[$key]=$value;
          }
        }
        $result=model_Job::UpdateJob($request);
        if($result)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Job Updated'));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'error / alredy Updated'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'invalid job id'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'invalid job id'));
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
    //echo $CurrentUser->access."==".$request['jobid']."==".$CurrentUser->status;
    if($CurrentUser->access=='Jobseeker' && $request['jobid'] && $CurrentUser->status==1)
    {
      $result=model_Job::JobApply($CurrentUser->id,$request['jobid']);
      if($result)
      {
        if($result['status'])
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result['data']));  
        }
        else
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'message'=>$result['data']));
        }
        
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Job id not found / job closed / Already Applied='.$result));
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

  function GetContractList()
  {
    //null, contract_id, job_id, job_seeker_id, companyid
    global $request,$CurrentUser;
    $request['companyid']=$request['employerid'];
    if($CurrentUser->access=="employer")
    {
      $request['companyid']=$CurrentUser->companyid;
    }
    if($CurrentUser->access=="Jobseeker")
    {
      $request['jobseekerid']=$CurrentUser->id;
    }
    
    $result=model_Job::GetContractList($request);
    if(is_array($result))
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Job'));
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

  function MatchedJob()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=="Jobseeker")
    {
      include_once('model/Jobseeker.php');
      $jobseeker_details=model_Jobseeker::GetJobseeker($CurrentUser->id);
      $MatchedData['employment_type']=$jobseeker_details[0]['employment_type'];
      $MatchedData['region']=$jobseeker_details[0]['region'];
      $MatchedData['location']=$jobseeker_details[0]['location'];
      $MatchedData['specializations']=$jobseeker_details[0]['specializations'];
      $MatchedData['working_environment']=str_replace(',','|',$jobseeker_details[0]['working_environment']);

      if($MatchedData['employment_type'] || $MatchedData['region'] || $MatchedData['location'] || $MatchedData['specializations'] || $MatchedData['working_environment'])
      {
        $result=model_Job::MatchedJob($MatchedData);
        if(is_array($result))
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
        }
        else {
          lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Matched Job'));
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'please update "Employment type", "Region", "location", "specializations", "working_environment"'));
      }

    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'only for jobseekers'));
    }
  }



// Updated By Sivaraj
  function DeleteJob()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access!='admin' && $CurrentUser->access!='employer' )
    {
      lib_ApiResult::JsonEncode(array('status'=>403,'result'=>'your not allow to delete'));
    }      

    if($CurrentUser->access!='admin')
    {
        if($CurrentUser->companyid>0)
        {
          $request['companyid']=$CurrentUser->companyid;
        }       
    }
    
    $result =model_Job::DeleteJob($request['jobid'],$request['companyid']);    
    if($result)
    {lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'deleted'));}
    else {
      lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'Invalid job id'));
    }
    
  }

}
