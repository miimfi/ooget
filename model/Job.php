<?php
include_once('model/Job.php');
class model_Job
{
  function CreateJob($request)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `job_list` (`job_name`, `department`, `employement_type`, `description`, `specializations`, `working_environment`, `pax`, `grace_period`, `over_time_rounding`, `over_time_start_from`, `from`, `to`, `start_time`, `end_time`, `work_days_type`, `postal_code`, `address`, `unit_no`, `region`, `location`, `charge_rate`, `markup_rate`, `markup_in`, `jobseeker_salary`, `markup_amount`,`auto_applide`, `auto_accepted`, `project_name`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssisssiiiissssisssssiisiiiis", $request['job_name'], $request['department'], $request['employement_type'], $request['description'], $request['specializations'], $request['working_environment'], $request['pax'], $request['grace_period'], $request['over_time_rounding'], $request['over_time_start_from'], $request['from'], $request['to'], $request['start_time'], $request['end_time'], $request['work_days_type'], $request['postal_code'], $request['address'], $request['unit_no'], $request['region'], $request['location'], $request['charge_rate'], $request['markup_rate'], $request['markup_in'], $request['jobseeker_salary'], $request['markup_amount'], $request['auto_applide'], $request['auto_accepted'], $request['project_name']);
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


    function GetJobList($employerid=0)
    {
      echo $employerid;
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

    function ChangeRecruitmentOpen($id,$status)
    {
      global $db;
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("UPDATE `job_list` SET `recruitment_open`=? WHERE  `id`=?");
      $sql->bind_param("ii", $status,$id);
      $sql->execute();
      return $sql->affected_rows;
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

    function AppliedJob($JobseekerId,$request)
    {
      global $db;
      $DBC=$db::dbconnect();
      $jobDetail=model_Job::GetJobDetails($request['jobid']);
      $CheckAlreadyApplied=model_Job::GetAppliedList($JobseekerId,$request['jobid']);
      if(!is_array($CheckAlreadyApplied) && $jobDetail['required']<$jobDetail['pax_total'] && $jobDetail['recruitment_open']==1 && $jobDetail['status']==1)
      {
        $applied_on=date("Y-m-d H:i:s");
        if($jobDetail['auto_applide']==1)
        {
          $offered_on=$applied_on;
        }

        $sql=$DBC->prepare("INSERT INTO `job_Applied` (`job_id`, `jobseeker_id`, `applied_on`, `offered_on`) VALUES (?, ?, ?, ?, )");
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

    function JobAccept($job_Applied)
    {

      $day;

      job_id,job_seeker_id,$date

      $sql="SELECT job_Applied.job_id, job_Applied.jobseeker_id, job_list.`from`, job_list.`to`, job_list.`work_days_type`, job_list.pax_total, job_list.required
FROM job_Applied
INNER JOIN job_list ON job_list.id=job_Applied.job_id
INNER JOIN jobseeker ON jobseeker.id=job_Applied.jobseeker_id
WHERE job_Applied.offer_accepted IS NULL AND
job_Applied.offer_rejected IS NULL AND
job_Applied.offered_on IS NOT NULL AND job_list.recruitment_open=1 AND job_Applied.id=?
";
      //CreateTimeSheet($data);
    }


    function GetAppliedList($JobseekerId,$jobid)
    {
      global $db;
      $DBC=$db::dbconnect();
      $select_query="SELECT job_Applied.*, job_list.project_name, job_list.job_name, job_list.department, job_list.`status` AS job_status, job_list.recruitment_open, jobseeker.firstname from `job_Applied` INNER JOIN job_list ON job_list.id=job_Applied.job_id INNER JOIN jobseeker ON jobseeker.id=job_Applied.jobseeker_id";
      if($JobseekerId>0 && $jobid>0)
      {
        $sql = $DBC->prepare($select_query." WHERE `jobseeker_id`=? AND `job_id`=? AND `deleted`!=1");
        $sql->bind_param("ii", $JobseekerId,$jobid);
      }
      else if ($JobseekerId>0 && !$jobid) {
        $sql = $DBC->prepare($select_query." WHERE `jobseeker_id`=? AND `deleted`!=1");
        $sql->bind_param("i", $JobseekerId);
      }
      else if(!$JobseekerId && $jobid>0)
      {
        $sql = $DBC->prepare($select_query." WHERE `job_id`=? AND `deleted`!=1");
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

    function CreateTimeSheet($data)
    {
      global $db;
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("INSERT INTO `time_sheet` (`job_id`, `job_seeker_id`, `date`, `day`, `ot_1_salary`, `ot_2_salary`, `salary`, `salary_total`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $sql->bind_param("iissiiii", $data['job_id'], $data['job_seeker_id'],  $data['date'], $data['day'], $data['ot_1_salary'], $data['ot_2_salary'], $data['salary'], $data['salary_total']);
      $sql->execute();
      $insertId=$sql->insert_id;
      $jobid=$insertId;
      $breaklist=$request['break'];
      ;
      return true;
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
