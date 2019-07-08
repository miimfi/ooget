<?php

class model_Job
{
  function CreateJob($request)
  {
    global $db,$MinimumOTHours;
    $DBC=$db::dbconnect();
    $date1=date("Y")."-01-01 00:00:00";
    $date2=date("Y")."-12-31 23:59:59";

// start job create number
    $sql_count=$DBC->prepare("SELECT `job_no`  FROM job_list WHERE `created_on` BETWEEN ? AND ? ORDER BY id DESC LIMIT 1");
    $sql_count->bind_param("ss", $date1, $date2);
    $sql_count->execute();
    $num_of_rows = $sql_count->num_rows;
    $result = $sql_count->get_result();
    while($row = $result->fetch_assoc()) {
        $JobCount= $row['job_no'];
      }
    $JobCount=explode('-',$JobCount);
    if(is_array($JobCount))
    {
      $job_no="OOGET-".date("Y")."-".sprintf("%04d", ($JobCount[2]+1));
    }
    else
    {
      $job_no="OOGET-".date("Y")."-0001";
    }
// end job create number

    if(!$request['over_time_minimum'])
    {
      $request['over_time_minimum']=$MinimumOTHours;
    }

    $sql=$DBC->prepare("INSERT INTO `job_list` (`job_name`, `department`, `employment_type`, `description`, `specializations`, `working_environment`, `pax_total`, `grace_period`, `over_time_rounding`, `over_time_minimum`, `from`, `to`, `start_time`, `end_time`, `work_days_type`, `postal_code`, `address`, `unit_no`, `region`, `location`, `charge_rate`, `markup_rate`, `markup_in`, `jobseeker_salary`, `markup_amount`,`auto_offered`, `auto_accepted`, `project_name`,`employer_id`,`job_no`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssisssiiiissssisssssiisiiiisisi", $request['job_name'], $request['department'], $request['employment_type'], $request['description'], $request['specializations'], $request['working_environment'], $request['pax_total'], $request['grace_period'], $request['over_time_rounding'], $request['over_time_minimum'], $request['from'], $request['to'], $request['start_time'], $request['end_time'], $request['work_days_type'], $request['postal_code'], $request['address'], $request['unit_no'], $request['region'], $request['location'], $request['charge_rate'], $request['markup_rate'], $request['markup_in'], $request['jobseeker_salary'], $request['markup_amount'], $request['auto_offered'], $request['auto_accepted'], $request['project_name'],$request['employer_id'],$job_no, $request['status']);
    $sql->execute();
    $insertId=$sql->insert_id;
    $jobid=$insertId;
    $breaklist=$request['break'];
    if(is_array($breaklist))
    {
        model_Job::AddBreak($jobid,$breaklist);
    }
    return $insertId;
  }

  function UpdateJob($request)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("UPDATE job_list SET `project_name` =?, `job_name` =?, `department` =?, `employment_type` =?, `description` =?, `specializations` =?, `working_environment` =?, `pax_total` =?, `grace_period` =?, `over_time_rounding` =?, `over_time_minimum` =?, `from` =?, `to` =?, `start_time` =?, `end_time` =?, `work_days_type` =?, `postal_code` =?, `address` =?, `unit_no` =?, `region` =?, `location` =?, `charge_rate` =?, `markup_rate` =?, `markup_in` =?, `jobseeker_salary` =?, `markup_amount` =?, `auto_offered` =?, `auto_accepted` =?, `job_no` =? WHERE `id`=? AND `employer_id`=?");
    $sql->bind_param("sssisssiiiissssisssssiisiiiisii",$request['project_name'], $request['job_name'], $request['department'], $request['employment_type'], $request['description'], $request['specializations'], $request['working_environment'], $request['pax_total'], $request['grace_period'], $request['over_time_rounding'],$request['over_time_minimum'], $request['from'], $request['to'], $request['start_time'], $request['end_time'], $request['work_days_type'], $request['postal_code'], $request['address'], $request['unit_no'], $request['region'], $request['location'], $request['charge_rate'], $request['markup_rate'], $request['markup_in'], $request['jobseeker_salary'], $request['markup_amount'], $request['auto_offered'], $request['auto_accepted'], $request['job_no'], $request['id'],$request['employer_id']);
    $sql->execute();

    if(is_array($request['break']))
    {
      $sql_break_delete=$DBC->prepare("DELETE FROM `job_break_list` WHERE  `job_id`=?");
      $sql_break_delete->bind_param('i',$request['id']);
      $sql_break_delete->execute();
      $breaklistUpdate=model_Job::AddBreak($request['id'],$request['break']);
    }

    return ($sql->affected_rows || $breaklistUpdate?true:false);

  }

    function GetAllJobList($employerid=0)
    {
        global $db;
        $DBC=$db::dbconnect();
        if($employerid>0)
        {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=?");
          $sql->bind_param("i",$employerid);
        }
        else {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id");
        }

        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
        {
          while($row = $result->fetch_assoc()) {
            $sqldata[] = $row;
          }
        }
        return $sqldata;
    }



    function GetOpenJobList($employerid=0,$saved_job_jobseeker_id)
    {
        global $db;
        $DBC=$db::dbconnect();
        if($employerid>0)
        {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND recruitment_open=1 AND job_list.status=2 AND company.status=2 AND job_list.`to`>=CURRENT_DATE()");
          $sql->bind_param("i",$employerid);
        }
        else {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE recruitment_open=1 AND job_list.status=2 AND company.status=2 AND job_list.`to`>=CURRENT_DATE()");
        }

        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($saved_job_jobseeker_id)
        {
            $SavedJob=model_Job::GetSaveJobList($saved_job_jobseeker_id);
        }

        if($num_of_rows>0)
        {
          while($row = $result->fetch_assoc()) {
            if($SavedJob[$row['id']])
            {
              $row['saved']=1;
            }

            $sqldata[] = $row;
          }
        }
        return $sqldata;
    }

    function GetLiveJobList($employerid=0)
    {

          global $db;
          $DBC=$db::dbconnect();
          if($employerid>0)
          {
            $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND job_list.status=2 AND company.status=2 AND job_list.`to`>=CURRENT_DATE()");
            $sql->bind_param("i",$employerid);
          }
          else {
            $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.status=2 AND company.status=2 AND job_list.`to`>=CURRENT_DATE()");
          }

          $sql->execute();
          $result = $sql->get_result();
          $num_of_rows = $result->num_rows;
          if($num_of_rows>0)
          {
            while($row = $result->fetch_assoc()) {
              $sqldata[] = $row;
            }
          }
          return $sqldata;
    }

    function GetClosedJobList($employerid=0)
    {
      global $db;
      $DBC=$db::dbconnect();
      if($employerid>0)
      {
        $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND job_list.status=3 AND company.status=2");
        $sql->bind_param("i",$employerid);
      }
      else {
        $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.status=3 AND company.status=2");
      }

      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $sqldata[] = $row;
        }
      }
      return $sqldata;
    }

    function GetPendingJobList($employerid=0)
    {
      global $db;
      $DBC=$db::dbconnect();
      if($employerid>0)
      {
        $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND job_list.status=1 AND company.status=2");
        $sql->bind_param("i",$employerid);
      }
      else {
        $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.status=1 AND company.status=2");
      }

      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $sqldata[] = $row;
        }
      }
      return $sqldata;
    }


    function GetJobDetails($id,$JobseekerId=0)
    {
        global $db;
        $DBC=$db::dbconnect();
        if($JobseekerId>0)
        {
            $applied_detailes=model_Job::GetAppliedList($JobseekerId,$id);
            if(is_array($applied_detailes[0]))
            {
              $AppliedData['applied_on']=$applied_detailes[0]['applied_on'];
              $AppliedData['offered_on']=$applied_detailes[0]['offered_on'];
              $AppliedData['offer_rejected']=$applied_detailes[0]['offer_rejected'];
              $AppliedData['offer_accepted']=$applied_detailes[0]['offer_accepted'];
              $AppliedData['contract_id']=$applied_detailes[0]['id'];
            }

            $SavedDetails=model_Job::GetSaveJobList($JobseekerId);
            //print_r($SavedDetails);exit;

        }

        $sql = $DBC->prepare("SELECT job_list.*, company.`name` as employer_name, company.`status` as employer_status, company.`profile` AS company_profile, company.`industry`, company.`imgpath` AS company_logo FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.`id`=?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
        {
          while($row = $result->fetch_assoc()) {
            $sqldata = $row;
          }
        }
        if(is_array($AppliedData))
        {
          $sqldata['jobseeker_applied_details']=$AppliedData;
        }
        if($SavedDetails[$id])
        {
          $sqldata['jobseeker_saved_data']=1;
        }
        // get job break time
        if(is_array($sqldata))
        {
        $sql = $DBC->prepare("SELECT `id` as break_id, `break_name`, `from`, `to`  FROM `job_break_list` WHERE `job_id`=?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
        {
          while($row = $result->fetch_assoc()) {
            $breaklist[] = $row;
          }
        }

        $sqldata['breaklist']=$breaklist;
        }
        return $sqldata;
    }



    function ChangeStatus($id,$status)
    {
      global $db;
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("UPDATE `job_list` SET `status`=? WHERE  `id`=?");
      $sql->bind_param("ii", $status,$id);
      $sql->execute();
      return $sql->affected_rows;
    }


//======================= for jobseeker ===================================
    function SaveJob($jobid, $jobseekerid)
    {

      global $db;
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("INSERT INTO `saved_job` (`job_id`, `jobseeker_id`) VALUES (?, ?)");
      $sql->bind_param("ii", $jobid,$jobseekerid);
      $sql->execute();
      $insertId=$sql->insert_id;
      return $insertId;
    }


    function RemoveSavedJob($jobid, $jobseekerid)
    {

      global $db;
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("DELETE FROM `saved_job` WHERE  `job_id`=? and `jobseeker_id`=?");
      $sql->bind_param("ii", $jobid,$jobseekerid);
      $sql->execute();
      $affectedData=$sql->affected_rows;
      if($affectedData>0)
      {
        return true;
      }
      else {
        return false;
      }
    }

      function GetSaveJobList($jobseekerid)
    {

      global $db;
      $DBC=$db::dbconnect();
      $sql = $DBC->prepare("SELECT `job_id` FROM saved_job WHERE `jobseeker_id`=?");
      $sql->bind_param("i", $jobseekerid);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $job_data[$row['job_id']]= 1;
        }
      }
      return $job_data;
    }



    function CheckJobseekerWorkingHours($jobseekerid,$to)
    {
        global $db;
        $DBC=$db::dbconnect();
        $from=date("Y-m-d");
        $sql = $DBC->prepare("SELECT job_list.start_time, job_list.end_time FROM `contracts`
          INNER JOIN job_list ON job_list.id=contracts.job_id INNER JOIN jobseeker ON jobseeker.id=contracts.jobseeker_id WHERE contracts.`jobseeker_id`=? AND (job_list.`from` BETWEEN ? AND ? OR job_list.`to` BETWEEN ? AND ?) AND contracts.`deleted`!=1");
        $sql->bind_param("issss", $jobseekerid,$from,$to,$from,$to);
        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
        {
          while($row = $result->fetch_assoc()) {
            $job_data[]= $row;
          }
        }
        return $job_data;
    }

    function JobApply($JobseekerId,$Jobid)
    {
      global $db;
	    $result_status=0;
      $DBC=$db::dbconnect();
      $jobDetail=model_Job::GetJobDetails($Jobid);
      $CheckAlreadyApplied=model_Job::CheckJobseekerWorkingHours($JobseekerId,$jobDetail['to']);
      if(!$CheckAlreadyApplied && $jobDetail['required']<$jobDetail['pax_total'] && $jobDetail['recruitment_open']==1 && $jobDetail['status']==2 && $jobDetail['employer_status']==2)
      {
        $applied_on=date("Y-m-d H:i:s");
        if($jobDetail['auto_offered']==1)
        {
          $offered_on=$applied_on;
        }

        $sql=$DBC->prepare("INSERT INTO `contracts` (`job_id`, `jobseeker_id`, `applied_on`, `offered_on`) VALUES (?, ?, ?, ? )");
        $sql->bind_param("iiss", $jobDetail['id'],$JobseekerId,$applied_on,$offered_on);
        $sql->execute();
        $insertId=$sql->insert_id;
		    if($insertId>0)
		    {
			       $result_status=1;
			       if($jobDetail['auto_offered']==1)
			          {
				           $result_status=2;
			          }
			       if($jobDetail['auto_accepted']==1)
			          {
				           $JobAccept_status=model_Job::JobAccept($JobseekerId,$insertId);
				           $result_status=3;
			          }
		}

        return $result_status;
      }
      else {
          return false;
      }
    }

    function GetJobBreakTime($jobid)
    {
      global $db;
      $DBC=$db::dbconnect();
      $sql = $DBC->prepare("SELECT break_name, `from` AS break_start, `to` AS break_end FROM job_break_list WHERE job_id=?");
      $sql->bind_param("i", $jobid);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $job_data[]= $row;
        }
      }
      return $job_data;
    }

    function JobOffered($Userid,$contracts_id)
    {
      global $db;
      $offered_on=date("Y-m-d H:i:s");
      $DBC=$db::dbconnect();
      $AppliedDetail=model_Job::AppliedDetail($contracts_id);
      if(!$AppliedDetail['offer_rejected'] && !$AppliedDetail['offer_accepted'] && !$AppliedDetail['offered_on'] && $AppliedDetail['deleted']!=1 )
      {
        $sql=$DBC->prepare("UPDATE `contracts` SET `offered_on`=? ,`user_id`=? WHERE  `id`=?");
        $sql->bind_param("sii", $offered_on, $Userid, $contracts_id);
        $sql->execute();
        $affectedData=$sql->affected_rows;
        if($affectedData>0)
        {
          return true;
        }
        else {
          return false;
        }
      }
      return false;
    }

    function ApplideReject($Userid,$contracts_id)
    {
      global $db;
      $offered_on=date("Y-m-d H:i:s");
      $DBC=$db::dbconnect();
      $AppliedDetail=model_Job::AppliedDetail($contracts_id);

      if(!$AppliedDetail['offer_rejected'] && !$AppliedDetail['offer_accepted'] && $AppliedDetail['deleted']!=1 )
      {
        //print_r($AppliedDetail);exit;
        $sql=$DBC->prepare("UPDATE `contracts` SET `offer_rejected`=? ,`user_id`=? WHERE  `id`=?");
        $sql->bind_param("sii", $offered_on, $Userid, $contracts_id);
        $sql->execute();
        $affectedData=$sql->affected_rows;
        if($affectedData>0)
        {
          return true;
        }
        else {
          return false;
        }
      }
      return false;
    }

    function JobAccept($jobseekerid,$contracts_id)
    {
      include_once('model/Timesheet.php');
      global $db;
      $accept_on=date("Y-m-d H:i:s");
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("SELECT contracts.id, contracts.job_id, contracts.jobseeker_id, job_list.`from`, job_list.`to`, job_list.`work_days_type`, job_list.pax_total, job_list.required FROM contracts
            INNER JOIN job_list ON job_list.id=contracts.job_id INNER JOIN jobseeker ON jobseeker.id=contracts.jobseeker_id
            WHERE contracts.offer_accepted IS NULL AND contracts.offer_rejected IS NULL AND contracts.offered_on IS NOT NULL AND job_list.recruitment_open=1 AND contracts.id=? AND jobseeker.id=?");
      $sql->bind_param("ii", $contracts_id, $jobseekerid);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $job_data= $row;
        }
      }

      if($job_data['id'])
      {
        //update job applied accepted time
        $sql2=$DBC->prepare("UPDATE `contracts` SET `offer_accepted`=? WHERE  `id`=?");
        $sql2->bind_param("si", $accept_on,$job_data['id']);
        $sql2->execute();
        $affected_contracts=$sql2->affected_rows;
        // update job_list required
        $required=$job_data['required']+1;
        if($affected_contracts)
        {
          if($job_data['pax_total']>$required)
          {
            $sql3=$DBC->prepare("UPDATE `job_list` SET `required`=? WHERE  `id`=?");
            $sql3->bind_param("ii", $required,$job_data['job_id']);

          }
          else {
            $recruitment_open=0;
            $sql3=$DBC->prepare("UPDATE `job_list` SET `required`=?, `recruitment_open`=? WHERE  `id`=?");
            $sql3->bind_param("iii", $required,$recruitment_open,$job_data['job_id']);
          }
          $sql3->execute();
          $affected_joblist=$sql3->affected_rows;
        }
      }
      else {
        return false;
      }

      $earlier = new DateTime($job_data['from']);
      $later = new DateTime($job_data['to']);
      $no_of_days = $later->diff($earlier)->format("%a");

      for ($i=0; $i <$no_of_days+1 ; $i++) {
        if($i==0)
        {
          $date=$job_data['from'];
        }
        else {
          $date=date('Y-m-d',strtotime($job_data['from'] . "+".$i." days"));
        }
        $is_timesheet=model_timesheet::CreateTimeSheet($contracts_id,$job_data['job_id'],$job_data['jobseeker_id'],$date,$job_data['$work_days_type']);
      }

      return $is_timesheet;
    }


    function GetAppliedList($JobseekerId=0,$jobid=0)
    {
      global $db;
      $DBC=$db::dbconnect();
      $select_query="SELECT contracts.*, company.companycode,job_list.pax_total,job_list.required,job_list.job_no,job_list.project_name, job_list.employment_type, job_list.location, job_list.job_name, job_list.department, job_list.`specializations`, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.`status` AS job_status, job_list.from as job_start_date, job_list.to as job_end_date, job_list.start_time as job_start_time, job_list.end_time as job_end_time,  job_list.recruitment_open, jobseeker.firstname as jobseeker_name, jobseeker.email as jobseeker_email, company.`name` as employer_name from `contracts` INNER JOIN job_list ON job_list.id=contracts.job_id INNER JOIN jobseeker ON jobseeker.id=contracts.jobseeker_id INNER JOIN company ON company.id=job_list.employer_id";

      if($JobseekerId>0 && $jobid>0)
      {
        //$sql = $DBC->prepare($select_query." WHERE contracts.`jobseeker_id`=? AND contracts.`job_id`=? AND contracts.offer_rejected IS NULL  AND contracts.offer_accepted IS NULL");
        $sql = $DBC->prepare($select_query." WHERE contracts.`jobseeker_id`=? AND contracts.`job_id`=? AND contracts.offer_rejected IS NULL");
        $sql->bind_param("ii", $JobseekerId,$jobid);
      }
      else if ($JobseekerId>0 && !$jobid) {
        $sql = $DBC->prepare($select_query." WHERE contracts.`jobseeker_id`=? AND contracts.`deleted`!=1 AND contracts.offer_rejected IS NULL  AND contracts.offer_accepted IS NULL");
        $sql->bind_param("i", $JobseekerId);
      }
      else if(!$JobseekerId && $jobid>0)
      {
        $sql = $DBC->prepare($select_query." WHERE contracts.`job_id`=? AND contracts.`deleted`!=1 AND contracts.offer_rejected IS NULL  AND contracts.offer_accepted IS NULL");
        $sql->bind_param("i", $jobid);
      }
      else {
        $sql = $DBC->prepare($select_query." WHERE  `deleted`!=1 AND contracts.offer_rejected IS NULL  AND contracts.offer_accepted IS NULL");
      }
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $data[] = $row;
        }
      }
      return $data;
    }

    function GetContractList($request)
    {
      global $db;
      $DBC=$db::dbconnect();
      $select_query="SELECT contracts.*, job_list.work_days_type, company.id AS employer_id,job_list.grace_period, jobseeker.phone as jobseeker_phone, jobseeker.account_no, company.companycode,job_list.pax_total,job_list.required,job_list.job_no,job_list.project_name, job_list.employment_type, job_list.location, job_list.job_name, job_list.department, job_list.`specializations`, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.`status` AS job_status, job_list.from as job_start_date, job_list.to as job_end_date, job_list.start_time as job_start_time, job_list.end_time as job_end_time,  job_list.recruitment_open, jobseeker.firstname as jobseeker_name, jobseeker.email as jobseeker_email, company.`name` as employer_name from `contracts` INNER JOIN job_list ON job_list.id=contracts.job_id INNER JOIN jobseeker ON jobseeker.id=contracts.jobseeker_id INNER JOIN company ON company.id=job_list.employer_id WHERE ";
      $select_query_post=" AND `deleted`!=1 AND contracts.offer_accepted IS NOT NULL ";

      //null, contract_id, job_id, jobseeker_id, companyid

      if($request['contractid']>0)
      {

        $select_filter="  contracts.`id`=? ";
        $select_filter_data=$request['contractid'];

      }elseif($request['jobid']>0)
      {

        $select_filter="  job_list.`id`=? ";
        $select_filter_data=$request['jobid'];

      }elseif($request['jobseekerid']>0)
      {

        $select_filter="  jobseeker.`id`=? ";
        $select_filter_data=$request['jobseekerid'];

      }


      if($request['companyid']>0)
      {
        if($select_filter)
        {
          $sql = $DBC->prepare($select_query.$select_filter." AND company.id=? ".$select_query_post);
          $sql->bind_param("ii", $select_filter_data,$request['companyid']);
        }
        else
        {
          $sql = $DBC->prepare($select_query." company.id=? ".$select_query_post);
          $sql->bind_param("i", $request['companyid']);
        }

      }else {
        if($select_filter)
        {
          $sql = $DBC->prepare($select_query.$select_filter.$select_query_post);
          $sql->bind_param("i", $select_filter_data);
        }
        else
        {
          $sql = $DBC->prepare($select_query." `deleted`!=1 AND contracts.offer_accepted IS NOT NULL ");
        }
      }

      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $data[] = $row;
        }
      }
      return $data;
    }

    function AppliedDetail($contracts_id)
    {
      global $db;
      $DBC=$db::dbconnect();
      $sql = $DBC->prepare("SELECT contracts.*, job_list.project_name, job_list.job_name, job_list.department, job_list.`status` AS job_status, job_list.recruitment_open, jobseeker.firstname as jobseeker_name, jobseeker.email AS jobseeker_email from `contracts` INNER JOIN job_list ON job_list.id=contracts.job_id INNER JOIN jobseeker ON jobseeker.id=contracts.jobseeker_id  WHERE contracts.`id`=? and contracts.`deleted`=0");
      $sql->bind_param("i", $contracts_id);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $sqldata = $row;
        }
      }
      return $sqldata;
    }


    function GetJobseekerContractList($JobseekerId)
    {
      global $db;
	    $today=date("Y-m-d");
      $DBC=$db::dbconnect();
      $sql = $DBC->prepare("SELECT contracts.*, job_list.job_name, job_list.project_name, job_list.`status` AS job_status, company.`name` AS company_name, job_list.`to` AS job_end  FROM  contracts
      INNER JOIN job_list ON job_list.id=contracts.job_id INNER JOIN company ON job_list.employer_id=company.id
	    WHERE  contracts.offer_accepted IS NOT NULL AND contracts.jobseeker_id=?");
      $sql->bind_param("i", $JobseekerId);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
			        $end_date=date("Y-m-d",strtotime($row['job_end']));
			        if($today>$end_date)
			          {
				            $row['deleted']=1;
			          }
              $sqldata[] = $row;
        }
      }
      return $sqldata;
    }

    function MatchedJob($MatchedData)
    {
      global $db;
      $DBC=$db::dbconnect();
      $query="SELECT * FROM job_list WHERE job_list.`status`=2";
      if($MatchedData['employment_type'])
      {
        $query.= " AND employment_type IN (".$MatchedData['employment_type'].")";
      }
      if($MatchedData['region'])
      {
        $query.= " AND region IN (".$MatchedData['region'].")";
      }
      if($MatchedData['location'])
      {
        $query.= " AND location IN (".$MatchedData['location'].")";
      }
      if($MatchedData['specializations'])
      {
        $query.= " AND specializations IN (".$MatchedData['specializations'].")";
      }
      if($MatchedData['working_environment'])
      {
        $query.=' AND CONCAT(",", `working_environment`, ",") REGEXP "('.$MatchedData['working_environment'].')"';
      }

      $sql = $DBC->prepare($query);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $sqldata[] = $row;
        }
      }
      return $sqldata;
    }



    function AddBreak($jobid,$breaklist)
    {
      global $db;
      $DBC=$db::dbconnect();
      foreach ($breaklist as $key => $value) {
        $sql=$DBC->prepare("INSERT INTO `job_break_list` (`break_name`, `job_id`, `from`, `to`) VALUES (?, ?, ?, ?)");
        $sql->bind_param("siss", $value->break_name, $jobid, $value->from, $value->to);
        $sql->execute();
      }
      $insertId=$sql->insert_id;
      return $insertId;
    }

}
