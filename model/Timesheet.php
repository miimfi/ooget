<?php
class model_timesheet
{

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
              $yesterdayJob=model_timesheet::GetJobseekerTimeSheet($jobseekerid,$yesterday,$yesterday);
            }
            if($sqldata['from']==$today || strtotime($sqldata['start_time'])<strtotime("+".$BeforePunchIn." minutes"))
            {
              $CurrentDayJob=model_timesheet::GetJobseekerTimeSheet($jobseekerid,$today,$today);
              $yesterdayJob=null;
            }


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
                return array('code' => 'success','data'=>$now_today );
              }
            }
             return array('code' => 'punch_in_error','data'=>'Punch in time not found' );
          }

          function VerifiedPunchOut($outtime,$timesheet_id,$userid)
          {
            // get job details
            global $db,$MinimumWorkingHours,$BeforePunchIn;
            $DBC=$db::dbconnect();
            $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_verified_out`=?, `clock_out_verified_by`=? WHERE  `id`=?");
            $sql2->bind_param("sii", $outtime,$userid,$timesheet_id);
            $sql2->execute();
            $affected_joblist=$sql2->affected_rows;
            if($affected_joblist>0)
            {
              return array('code' => 'success','data'=>$outtime );
            }

             return array('code' => 'punch_in_error','data'=>'Punch in time not found' );
          }

          function VerifiedPunchIn($intime,$timesheet_id,$userid)
          {
            // get job details
            global $db,$MinimumWorkingHours,$BeforePunchIn;
            $DBC=$db::dbconnect();
            $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_verified_in`=?, `clock_in_verified_by`=? WHERE  `id`=?");
            $sql2->bind_param("sii", $intime,$userid,$timesheet_id);
            $sql2->execute();
            $affected_joblist=$sql2->affected_rows;
            if($affected_joblist>0)
            {
              return array('code' => 'success','data'=>$intime );
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

}


 ?>
