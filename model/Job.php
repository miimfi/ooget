<?php
include_once('model/Job.php');
class model_Job
{
  function CreateJob($request)
  {
    global $db;
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
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND recruitment_open=1 AND job_list.status=2 AND company.status=2");
          $sql->bind_param("i",$employerid);
        }
        else {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE recruitment_open=1 AND job_list.status=2 AND company.status=2");
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
            $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND job_list.status=2 AND company.status=2");
            $sql->bind_param("i",$employerid);
          }
          else {
            $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employment_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company, company.imgpath as companylogo, job_list.jobseeker_salary, job_list.location, job_list.`specializations` FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.status=2 AND company.status=2");
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
        $sql = $DBC->prepare("SELECT job_break_list.`id` as break_id, `break_name`, `from`, `to`  FROM `job_break_list` WHERE `job_id`=?");
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



    function CheckJobseekerWorkingHours($jobseekerid,$from,$to)
    {
        global $db;
        $DBC=$db::dbconnect();
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
      $DBC=$db::dbconnect();
      $jobDetail=model_Job::GetJobDetails($Jobid);
    // print_r($jobDetail);//exit;
      $CheckAlreadyApplied=model_Job::CheckJobseekerWorkingHours($JobseekerId,$jobDetail['from'],$jobDetail['to']);
    //  print_r($CheckAlreadyApplied);exit;
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

        if($jobDetail['auto_accepted']==1 && $insertId>0)
        {
          $JobAccept_status=model_Job::JobAccept($insertId);

        }
        return $insertId;
      }
      else {
          return false;
      }
    }



    function JobOffered($Userid,$contracts_id)
    {
      global $db;
      $offered_on=date("Y-m-d H:i:s");
      $DBC=$db::dbconnect();
      $AppliedDetail=model_Job::AppliedDetail($contracts_id);
//print_r($AppliedDetail);exit;
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
      /*echo "==================<br>";
      print_r($job_data);*/
      //$data['job_id'], $data['job_seeker_id'],  $data['date'], $data['day']
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
      //  echo $job_data['job_id']."=".$job_data['jobseeker_id']."=".$date;
        $is_timesheet=model_Job::CreateTimeSheet($contracts_id,$job_data['job_id'],$job_data['jobseeker_id'],$date,$job_data['$work_days_type']);
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
        $sql = $DBC->prepare($select_query." WHERE contracts.`jobseeker_id`=? AND contracts.`job_id`=?");

        $sql->bind_param("ii", $JobseekerId,$jobid);
      }
      else if ($JobseekerId>0 && !$jobid) {
        $sql = $DBC->prepare($select_query." WHERE contracts.`jobseeker_id`=? AND contracts.`deleted`!=1");
        $sql->bind_param("i", $JobseekerId);
      }
      else if(!$JobseekerId && $jobid>0)
      {
        $sql = $DBC->prepare($select_query." WHERE contracts.`job_id`=? AND contracts.`deleted`!=1");
        $sql->bind_param("i", $jobid);
      }
      else {
        $sql = $DBC->prepare($select_query." WHERE  `deleted`!=1 ");
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

    function CheckJobseekerTimeSheet($JobseekerId,$jobid)
    {
      return true;
    }

    function GetJobseekerTimeSheet($JobseekerId,$from,$to)
    {
      global $db;
      $DBC=$db::dbconnect();
      $sql = $DBC->prepare("SELECT * FROM time_sheet WHERE `job_seeker_id`=? AND `date` BETWEEN  ? AND ?");
      $sql->bind_param("iss", $JobseekerId,$from,$to);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $sqldata[] = $row;
        }
      }
      //print_r($sqldata);
      return $sqldata;
    }

    function GetJobseekerContractList($JobseekerId)
    {
      global $db;
      $DBC=$db::dbconnect();
      $sql = $DBC->prepare("SELECT contracts.*, job_list.job_name, job_list.project_name, job_list.`status` AS job_status, company.`name` AS company_name  FROM  contracts
      INNER JOIN job_list ON job_list.id=contracts.job_id INNER JOIN company ON job_list.employer_id=company.id
      WHERE  contracts.offer_accepted IS NOT NULL AND contracts.jobseeker_id=?");
      $sql->bind_param("i", $JobseekerId);
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

    function GetTodayJobseekerTimeSheet($jobseekerid, $contractid)
    {
      global $db,$BeforePunchIn;
      $DBC=$db::dbconnect();
      $today=date("Y-m-d");
      $yesterday=date('Y-m-d', strtotime('-1 days'));
      $nextday=date('Y-m-d', strtotime('+1 days'));
      // get job details
      $sql = $DBC->prepare("SELECT contracts.`deleted` as contarct_status, job_list.job_name, job_list.`status` AS job_status, company.`name` AS company_name, job_list.grace_period, job_list.start_time, job_list.end_time, job_list.job_no, job_list.`from`,job_list.`to` FROM  contracts
      INNER JOIN job_list ON job_list.id=contracts.job_id INNER JOIN company ON job_list.employer_id=company.id
      WHERE contracts.id=? AND contracts.offer_accepted IS NOT NULL");
      $sql->bind_param("i", $contractid);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $sqldata= $row;
        }
      }
      $jobtime['start_time']=$sqldata['start_time'];
      $jobtime['end_time']=$sqldata['end_time'];

      if($sqldata['contarct_status']==1)
      {
        return array('contract_status' => $sqldata['contarct_status'],'result'=> "contract closed");
      }
      //$sqldata['from']="2019-05-29";
      if($sqldata['from']<$today)
      {
        $yesterdayJob=model_Job::GetJobseekerTimeSheet($jobseekerid,$yesterday,$yesterday);
      }
      if($sqldata['from']==$today || strtotime($sqldata['start_time'])<strtotime("+".$BeforePunchIn." minutes"))
      {
        $CurrentDayJob=model_Job::GetJobseekerTimeSheet($jobseekerid,$today,$today);
        $yesterdayJob=null;
      }
    //  print_r($CurrentDayJob[0]);exit;
      /*if($sqldata['to']>$today)
      {
          $nextdayJob=model_Job::GetJobseekerTimeSheet($jobseekerid,$nextday,$nextday);
      }*/

      if(!$CurrentDayJob && !$yesterdayJob)
      {
        return array('contract_status' => $sqldata['job_status'],'result'=> "Your contract start at ".$sqldata['from']." - ".$sqldata['start_time']);

      }
      else if ($CurrentDayJob && !$yesterdayJob && strtotime($sqldata['start_time'])>strtotime("+".$BeforePunchIn." minutes")) {
        return array('contract_status' => $sqldata['contarct_status'],'result'=> "Your contract start today at ".$sqldata['start_time']);

      }else if ($yesterdayJob && strtotime($sqldata['start_time'])>strtotime("-".$BeforePunchIn." minutes")) {
        return array('contract_status' => $sqldata['contarct_status'],'result'=> $yesterdayJob[0], 'job_time'=>$jobtime);
      }
      else if($CurrentDayJob && strtotime($sqldata['start_time'])<strtotime("+".$BeforePunchIn." minutes"))
      {
        return array('contract_status' => $sqldata['contarct_status'],'result'=> $CurrentDayJob[0], 'job_time'=>$jobtime);
      }
      //$getJobdetails=model_Job::
      return array('contract_status' => $sqldata['contarct_status'],'result'=> "contract closed");
    }

    function PunchIn($jobseekerid,$timesheet_id)
    {
      $today=date("Y-m-d");
      $now_today=date("Y-m-d H:i:s");
      $yesterday=date('Y-m-d', strtotime('-1 days'));
      $nextday=date('Y-m-d', strtotime('+1 days'));

      // get job details
      global $db,$BeforePunchIn;
      $DBC=$db::dbconnect();
      $sql = $DBC->prepare("SELECT time_sheet.clock_verified_in, time_sheet.clock_in, job_list.start_time, job_list.end_time, time_sheet.date FROM time_sheet
        INNER JOIN contracts ON contracts.id = time_sheet.contracts_id INNER JOIN job_list ON job_list.id = contracts.job_id
        WHERE time_sheet.`id`=? AND time_sheet.`job_seeker_id`=? AND time_sheet.`clock_in` IS NULL AND contracts.`deleted`=0 AND time_sheet.`date` BETWEEN ? AND ?");
      $sql->bind_param("iiss", $timesheet_id, $jobseekerid, $yesterday, $today);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $sqldata= $row;
        }
      }

      if($sqldata['clock_verified_in'])
      {
        return "Already verified : ".$sqldata['clock_verified_in'];
      }
      else {
        if($sqldata['date']==$yesterday && $sqldata['start_time']>$sqldata['end_time'] && strtotime($sqldata['end_time'])<strtotime("-".$BeforePunchIn." minutes"))
        {
          // code for punch yesterday
          $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_in`=? WHERE  `id`=?");
          $sql2->bind_param("si", $now_today,$timesheet_id);
          $sql2->execute();
          $affected_joblist=$sql2->affected_rows;
        }
        else if($sqldata['date']==$today)
        {
          if(strtotime($sqldata['start_time'])<strtotime("+".$BeforePunchIn." minutes"))
          {
            // normal punch
            $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_in`=? WHERE  `id`=?");
            $sql2->bind_param("si", $now_today,$timesheet_id);
            $sql2->execute();
            $affected_joblist=$sql2->affected_rows;
          }
          else {
            return "your job start at ".$sqldata['start_time'].". ( your PUNCH_IN start before ".$BeforePunchIn." min from job start time )";
          }

        }

        if($affected_joblist>0)
        {
          return $now_today;
        }
      }
      return false;
    }

    function PunchOut($jobseekerid,$timesheet_id)
    {
      $today=date("Y-m-d");
      $now_today=date("Y-m-d H:i:s");
      $yesterday=date('Y-m-d', strtotime('-1 days'));
      $nextday=date('Y-m-d', strtotime('+1 days'));

      // get job details
      global $db,$MinimumWorkingHours,$BeforePunchIn;
      $DBC=$db::dbconnect();
      $sql = $DBC->prepare("SELECT time_sheet.clock_verified_in, time_sheet.clock_verified_out, time_sheet.clock_in, time_sheet.clock_out, job_list.start_time, job_list.end_time, time_sheet.date FROM time_sheet
        INNER JOIN contracts ON contracts.id = time_sheet.contracts_id INNER JOIN job_list ON job_list.id = contracts.job_id
        WHERE time_sheet.`id`=? AND time_sheet.`job_seeker_id`=? AND (time_sheet.`clock_in` IS NOT NULL OR time_sheet.`clock_verified_in` IS NOT NULL) AND contracts.`deleted`=0 AND time_sheet.`date` BETWEEN ? AND ?");
      $sql->bind_param("iiss", $timesheet_id, $jobseekerid, $yesterday, $today);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $sqldata= $row;
        }
      }

      if($sqldata['clock_verified_out'] || $sqldata['clock_out'])
      {
        $C_status="Already ".($sqldata['clock_verified_out']?"verified : ".$sqldata['clock_verified_out']:"punch out at ".$sqldata['clock_out']);
        return array('code' => ($sqldata['clock_verified_out']?'clock_out_verified':'already_punch_out'),'data'=>$C_status );
      }
      else {
        if($sqldata['date']==$yesterday && strtotime($sqldata['start_time'])>strtotime("-".$BeforePunchIn." minutes"))
        {
          // code for punch yesterday
          $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_out`=? WHERE  `id`=?");
          $sql2->bind_param("si", $now_today,$timesheet_id);
          $sql2->execute();
          $affected_joblist=$sql2->affected_rows;
        }
        else if($sqldata['date']==$today)
        {
          //echo strtotime(date($today." ".$sqldata['start_time']));exit;
          //echo strtotime($sqldata['clock_in'])+($MinimumWorkingHours*60);exit;
          //echo strtotime($sqldata['clock_in']);exit;
          if(strtotime($now_today)>(strtotime($sqldata['clock_in'])+($MinimumWorkingHours*60)))
          {
            // normal punch
            $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_out`=? WHERE  `id`=?");
            $sql2->bind_param("si", $now_today,$timesheet_id);
            $sql2->execute();
            $affected_joblist=$sql2->affected_rows;
          }
          else {
            return array('code' => 'working_hours_low','data'=>"Minimum working hours ".$MinimumWorkingHours."min, Your punch_in at ".$sqldata['clock_in'] );
          }

        }

        if($affected_joblist>0)
        {
          return array('code' => 'succes','data'=>$now_today );
        }
      }
       return array('code' => 'punch_in_error','data'=>'Punch in time not found' );
    }


    function CreateTimeSheet($contracts_id,$jobid,$jobseekerid,$date,$work_days_type)
    {
      include_once('model/Holiday.php');
      $Request_data['date'] =$date;
      $holiday=model_holiday::GetHoliday($Request_data);
      global $db;
      $day=date("D",strtotime($date));
      $DBC=$db::dbconnect();
      if(is_array($holiday))
      {
        $is_holiday='P';
      }
      else if ($work_days_type!=1 && ($day=='Sat' || $day=='Sun')) {
        $is_holiday='Y';
      }
      else {
        $is_holiday='N';
      }
      $sql=$DBC->prepare("INSERT INTO `time_sheet` (`contracts_id`,`job_id`, `job_seeker_id`, `date`, `day`, `holiday` ) VALUES (?, ?, ?, ?, ?, ?)");
      $sql->bind_param("iiisss", $contracts_id, $jobid, $jobseekerid, $date, $day, $is_holiday);
      $sql->execute();
      $insertId=$sql->insert_id;
      return $insertId;
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
