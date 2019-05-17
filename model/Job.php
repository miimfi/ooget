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
        global $db;
        $DBC=$db::dbconnect();
        if($employerid>0)
        {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax`,job_list.`job_required`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id WHERE job_list.employer_id=?");
          $sql->bind_param("i",$employerid);
        }
        else {
          $sql = $DBC->prepare("SELECT job_list.`id`, job_list.`job_no`, job_list.`project_name`, job_list.`job_name`, job_list.`department`, job_list.`employement_type`, job_list.`status`, job_list.`pax`,job_list.`job_required`,job_list.`from`,job_list.`to`, job_list.`employer_id`, company.`name` as company FROM `job_list` INNER JOIN company ON company.id=job_list.employer_id");
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
            $sqldata[] = $row;
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

        $sqldata[0]['breaklist']=$breaklist;
        }
        return $sqldata;
    }

    function ChangeStatus($status)
    {

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
