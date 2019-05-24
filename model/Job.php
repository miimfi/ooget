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

    $sql=$DBC->prepare("INSERT INTO `job_list` (`job_name`, `department`, `employement_type`, `description`, `specializations`, `working_environment`, `pax_total`, `grace_period`, `over_time_rounding`, `over_time_start_from`, `from`, `to`, `start_time`, `end_time`, `work_days_type`, `postal_code`, `address`, `unit_no`, `region`, `location`, `charge_rate`, `markup_rate`, `markup_in`, `jobseeker_salary`, `markup_amount`,`auto_applide`, `auto_accepted`, `project_name`,`employer_id`,`job_no`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssisssiiiissssisssssiisiiiisisi", $request['job_name'], $request['department'], $request['employement_type'], $request['description'], $request['specializations'], $request['working_environment'], $request['pax_total'], $request['grace_period'], $request['over_time_rounding'], $request['over_time_start_from'], $request['from'], $request['to'], $request['start_time'], $request['end_time'], $request['work_days_type'], $request['postal_code'], $request['address'], $request['unit_no'], $request['region'], $request['location'], $request['charge_rate'], $request['markup_rate'], $request['markup_in'], $request['jobseeker_salary'], $request['markup_amount'], $request['auto_applide'], $request['auto_accepted'], $request['project_name'],$request['employer_id'],$job_no, $request['status']);
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
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=?");
          $sql->bind_param("i",$employerid);
        }
        else {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id");
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

    function GetOpenJobList($employerid=0)
    {
        global $db;
        $DBC=$db::dbconnect();
        if($employerid>0)
        {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND recruitment_open=1 AND job_list.status=2 AND company.status=2");
          $sql->bind_param("i",$employerid);
        }
        else {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE recruitment_open=1 AND job_list.status=2 AND company.status=2");
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

    function GetLiveJobList($employerid=0)
    {

          global $db;
          $DBC=$db::dbconnect();
          if($employerid>0)
          {
            $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND job_list.status=2 AND company.status=2");
            $sql->bind_param("i",$employerid);
          }
          else {
            $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.status=2 AND company.status=2");
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
        $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND job_list.status=3 AND company.status=2");
        $sql->bind_param("i",$employerid);
      }
      else {
        $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.status=3 AND company.status=2");
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
        $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=? AND job_list.status=1 AND company.status=2");
        $sql->bind_param("i",$employerid);
      }
      else {
        $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax_total`,job_list.`required`,job_list.`recruitment_open`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.status=1 AND company.status=2");
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


    function GetJobDetails($id)
    {
        global $db;
        $DBC=$db::dbconnect();
        $sql = $DBC->prepare("SELECT job_list.*, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.`id`=?");
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


//==========================================================

    function AppliedJob($JobseekerId,$Jobid)
    {
      global $db;
      $DBC=$db::dbconnect();
      $jobDetail=model_Job::GetJobDetails($Jobid);
      //print_r($jobDetail);exit;
      $CheckAlreadyApplied=model_Job::GetAppliedList($JobseekerId,$Jobid);
      if(!is_array($CheckAlreadyApplied) && $jobDetail['required']<$jobDetail['pax_total'] && $jobDetail['recruitment_open']==1 && $jobDetail['status']==1)
      {
        $applied_on=date("Y-m-d H:i:s");
        if($jobDetail['auto_applide']==1)
        {
          $offered_on=$applied_on;
        }

        $sql=$DBC->prepare("INSERT INTO `job_Applied` (`job_id`, `jobseeker_id`, `applied_on`, `offered_on`) VALUES (?, ?, ?, ? )");
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

    function JobAccept($job_Applied_id)
    {
      global $db;
      $accept_on=date("Y-m-d H:i:s");
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("SELECT job_Applied.id, job_Applied.job_id, job_Applied.jobseeker_id, job_list.`from`, job_list.`to`, job_list.`work_days_type`, job_list.pax_total, job_list.required FROM job_Applied
            INNER JOIN job_list ON job_list.id=job_Applied.job_id INNER JOIN jobseeker ON jobseeker.id=job_Applied.jobseeker_id
            WHERE job_Applied.offer_accepted IS NULL AND job_Applied.offer_rejected IS NULL AND job_Applied.offered_on IS NOT NULL AND job_list.recruitment_open=1 AND job_Applied.id=?");
      $sql->bind_param("i", $job_Applied_id);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $job_data= $row;
        }
      }
    //  return $job_data;
      if($job_data['id'])
      {
        //update job applied accepted time
        $sql2=$DBC->prepare("UPDATE `job_Applied` SET `offer_accepted`=? WHERE  `id`=?");
        $sql2->bind_param("si", $accept_on,$job_data['id']);
        $sql2->execute();
        $affected_job_Applied=$sql2->affected_rows;
        // update job_list required
        $required=$job_data['required']+1;
        if($affected_job_Applied)
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
      echo "test fsdfsdfsdf";
      print_r($job_data);
      //$data['job_id'], $data['job_seeker_id'],  $data['date'], $data['day']
      $earlier = new DateTime($job_data['from']);
      $later = new DateTime($job_data['to']);
      $no_of_days = $later->diff($earlier)->format("%a");
      for ($i=0; $i <$no_of_days ; $i++) {
        if($i==0)
        {
          $date=$job_data['from'];
        }
        else {
          $date=date('Y-m-d',strtotime($job_data['from'] . "+".$i." days"));
        }
        echo $job_data['job_id']."=".$job_data['jobseeker_id']."=".$date;
        $is_timesheet=model_Job::CreateTimeSheet($job_data['job_id'],$job_data['jobseeker_id'],$date);
      }

      return $is_timesheet;
    }


    function GetAppliedList($JobseekerId,$jobid)
    {
      global $db;
      $DBC=$db::dbconnect();
      $select_query="SELECT job_Applied.*, job_list.project_name, job_list.job_name, job_list.department, job_list.`status` AS job_status, job_list.recruitment_open, jobseeker.firstname from `job_Applied` INNER JOIN job_list ON job_list.id=job_Applied.job_id INNER JOIN jobseeker ON jobseeker.id=job_Applied.jobseeker_id";
      if($JobseekerId>0 && $jobid>0)
      {
        $sql = $DBC->prepare($select_query." WHERE job_Applied.`jobseeker_id`=? AND job_Applied.`job_id`=? AND job_Applied.`deleted`!=1");

        $sql->bind_param("ii", $JobseekerId,$jobid);
      }
      else if ($JobseekerId>0 && !$jobid) {
        $sql = $DBC->prepare($select_query." WHERE job_Applied.`jobseeker_id`=? AND job_Applied.`deleted`!=1");
        $sql->bind_param("i", $JobseekerId);
      }
      else if(!$JobseekerId && $jobid>0)
      {
        $sql = $DBC->prepare($select_query." WHERE job_Applied.`job_id`=? AND job_Applied.`deleted`!=1");
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



    function CheckJobseekerTimeSheet($JobseekerId,$jobid)
    {
      return true;
    }

    function CreateTimeSheet($jobid,$jobseekerid,$date)
    {

      global $db;
      $day=date("D",strtotime($date));
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("INSERT INTO `time_sheet` (`job_id`, `job_seeker_id`, `date`, `day`) VALUES (?, ?, ?, ?)");
      $sql->bind_param("iiss",  $jobid, $jobseekerid, $date, $day);
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
