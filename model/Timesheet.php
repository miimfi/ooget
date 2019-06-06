<?php
class model_timesheet
{

      function CheckJobseekerTimeSheet($JobseekerId,$jobid)
      {
        return true;
      }

      function GetTimeSheet($request)
      {

        global $db;
        $DBC=$db::dbconnect();

        $data_type='i';
        $params[]=$request['contractid'];
        $sql_query="SELECT * FROM time_sheet WHERE `contracts_id`=? ";

        if($request['timesheet_id'])
        {
          $sql_query="SELECT * FROM time_sheet WHERE `id`=? ";
          $data_type='i';
          $params[0]=$request['timesheet_id'];
        }
        if($request['jobseekerid'])
        {
          $sql_query.=" AND `jobseeker_id`=? ";
          $data_type.='i';
          $params[]=$request['jobseekerid'];
        }
        if($request['from'] && $request['to'])
        {
          $sql_query.=" AND `date` BETWEEN  ? AND ? ";
          $data_type.='ss';
          $params[]=$request['from'];
          $params[]=$request['to'];
        }


        $sql = $DBC->prepare($sql_query);
        $sql->bind_param($data_type, ...$params);
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
            $nextday_addday=date('Y-m-d', strtotime('+2 days'));
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
            $jobtime['from']=$sqldata['from'];
            $jobtime['to']=$sqldata['to'];

            if($sqldata['contarct_status']!=1)
            {
              $contract_status='open';
            }

            if($sqldata['contarct_status']==1 || $sqldata['to']<$yesterday)
            {
              return array('contract_status' => 'closed','result'=> "contract closed");
            }

            $request_dta['contractid']=$contractid;
            $request_dta['from']=$yesterday;
            $request_dta['to']=$yesterday;
            $yesterdayJob=model_timesheet::GetTimeSheet($request_dta);

            $request_dta['contractid']=$contractid;
            $request_dta['from']=$today;
            $request_dta['to']=$today;
            $CurrentDayJob=model_timesheet::GetTimeSheet($request_dta);

            if($sqldata['from']>$today) {
                return array('contract_status' => $contract_status,'result'=> "Your contract start at ".$sqldata['from']." - ".$sqldata['start_time'],'job_time'=>$jobtime);
            }
            elseif(!$yesterdayJob[0]['clock_verified_out'] && !$yesterdayJob[0]['clock_out'] && ( $yesterdayJob[0]['clock_in'] || $yesterdayJob[0]['clock_verified_in'] ) && strtotime($sqldata['start_time'])>strtotime("+".$BeforePunchIn." minutes") )
            {
                return array('contract_status' => $contract_status,'result'=> $yesterdayJob,'job_time'=>$jobtime);
            }
            elseif($CurrentDayJob[0] && strtotime($sqldata['start_time'])<strtotime("+".$BeforePunchIn." minutes"))
            {
                return array('contract_status' => $contract_status,'result'=> $CurrentDayJob,'job_time'=>$jobtime);
            }elseif ($CurrentDayJob[0] && strtotime($sqldata['start_time'])>strtotime("+".$BeforePunchIn." minutes")){
              return array('contract_status' => $contract_status,'result'=> "Your job start at today - ".$sqldata['start_time'],'job_time'=>$jobtime);
            }
            else
            {
              return array('contract_status' => 'closed','result'=> "contract closed");
            }
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
              WHERE time_sheet.`id`=? AND time_sheet.`jobseeker_id`=? AND time_sheet.`clock_in` IS NULL AND contracts.`deleted`=0 AND time_sheet.`date` BETWEEN ? AND ?");
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
            global $db,$MinimumWorkingHours,$BeforePunchIn,$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,$PublicHolidayOTSalary,$WorkingHoursRound;
            $DBC=$db::dbconnect();
            $sql = $DBC->prepare("SELECT
              time_sheet.clock_verified_in, time_sheet.holiday, time_sheet.clock_verified_out, time_sheet.clock_in, time_sheet.clock_out, job_list.start_time, job_list.end_time, time_sheet.DATE, job_list.grace_period,job_list.over_time_rounding,job_list.over_time_minimum,job_list.work_days_type,job_list.jobseeker_salary
              FROM time_sheet INNER JOIN contracts ON contracts.id = time_sheet.contracts_id INNER JOIN job_list ON job_list.id = contracts.job_id
              WHERE time_sheet.`id`=? AND time_sheet.`jobseeker_id`=? AND (time_sheet.`clock_in` IS NOT NULL OR time_sheet.`clock_verified_in` IS NOT NULL) AND contracts.`deleted`=0 AND time_sheet.`date` BETWEEN ? AND ?");
            $sql->bind_param("iiss", $timesheet_id, $jobseekerid, $yesterday, $today);
            $sql->execute();
            $result = $sql->get_result();
            $num_of_rows = $result->num_rows;
            if($num_of_rows>0)
            {
              while($row = $result->fetch_assoc()) {
                $sqldata= $row;
              }
            }else {
              return array('code' => 'timesheet_not_found','data'=>"Timesheet not found" );
            }


            if($sqldata['clock_verified_out'] || $sqldata['clock_out'])
            {
              $C_status="Already ".($sqldata['clock_verified_out']?"verified : ".$sqldata['clock_verified_out']:"punch out at ".$sqldata['clock_out']);
              return array('code' => ($sqldata['clock_verified_out']?'clock_out_verified':'already_punch_out'),'data'=>$C_status );
            }
            else {

              if(strtotime($now_today)>(strtotime($sqldata['clock_in'])+($MinimumWorkingHours*60)))
              {
                /*$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,
                $PublicHolidayOTSalary,$WorkingHoursRound */
                print_r($sqldata);

                //set job salary based on day tipe
                $salary=$sqldata['jobseeker_salary'];
                if($sqldata['holiday']=='Y')
                {
                  $salary=$salary*$HolidaySalary;
                }
                if($sqldata['holiday']=='P')
                {
                  $salary=$salary*$PublicHolidaySalary;
                }
                $salaryPerMini=$salary/60;

                //set OT salary
                $OTsalary=$sqldata['jobseeker_salary'];
                if($sqldata['holiday']=='Y')
                {
                  $OTsalary=$OTsalary*$HolidayOTSalary;
                }elseif($sqldata['holiday']=='P')
                {
                  $OTsalary=$OTsalary*$PublicHolidayOTSalary;
                }
                else {
                  $OTsalary=$OTsalary*$OverTimetSalary;
                }
                $OTsalaryMin=$OTsalary/60;

                $TotalWorkingMin=0;$JobWorkingMin=0;
                if(strtotime($sqldata['end_time'])>strtotime($sqldata['start_time']))
                {
                    $JobWorkingMin=(strtotime($sqldata['end_time'])-strtotime($sqldata['start_time']))/60;
                }
                else {
                  $JobWorkingMin=((strtotime($sqldata['end_time']+86400))-strtotime($sqldata['start_time']))/60;
                }

                $TotalWorkingMin=round((strtotime($now_today)-strtotime(($sqldata['clock_verified_in']?$sqldata['clock_verified_in']:$sqldata['clock_in'])))/60);

                //ot time calculation
                $JobseekerOTmin=0;
                if(($JobWorkingMin+$sqldata['over_time_minimum'])<=$TotalWorkingMin)
                {
                  $JobseekerOTmin=$TotalWorkingMin-$JobWorkingMin;
                  if($sqldata['over_time_rounding'])
                  {
                    $OTnonecountmin=$JobseekerOTmin%$sqldata['over_time_rounding']; // over time round
                    $JobseekerOTmin=$JobseekerOTmin-$OTnonecountmin;
                  }
                }

                // calculate narmal salary


                if($JobWorkingMin<=$TotalWorkingMin)
                {
                  //working hours is above job hours
                  $JobseekerSalary=$JobWorkingMin*$salaryPerMini;
                }
                else {
                  // below job hours
                  if($WorkingHoursRound)
                  {
                    $normalMinnonecount=0;
                    $normalMinnonecount=$TotalWorkingMin%$WorkingHoursRound;
                    $TotalWorkingMin=$TotalWorkingMin-$normalMinnonecount;
                  }
                  $JobseekerSalary=$TotalWorkingMin*$salaryPerMini;
                }

                $JobseekerOTSalary=$JobseekerOTmin*$OTsalaryMin;
                $TotalJobSeekerSalary=$JobseekerOTSalary+$JobseekerSalary;

                $timesheetResult['PunchOut']=$now_today;
                $timesheetResult['Salary']=$salary;
                $timesheetResult['SlaryPerMini']=$salaryPerMini;
                $timesheetResult['OTslaryMini']=$OTsalaryMin;
                $timesheetResult['TotalWorkingmin']=$TotalWorkingMin;
                $timesheetResult['JobWorkingMin']=$JobWorkingMin;
                $timesheetResult['OTTimeMin']=$JobseekerOTmin;
                $timesheetResult['JobseekerSalary']=$JobseekerSalary;
                $timesheetResult['JobseekerOTSalary']=$JobseekerOTSalary;
                $timesheetResult['TotalJobSeekerSalary']=$TotalJobSeekerSalary;

                $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_out`=?, `ot_salary`=?, `salary`=?, `salary_total`=? WHERE  `id`=?");
                $sql2->bind_param("sdddi", $now_today,$JobseekerOTSalary,$JobseekerSalary,$TotalJobSeekerSalary,$timesheet_id);
                $sql2->execute();
                $affected_joblist=$sql2->affected_rows;
              }
              else {
                  return array('code' => 'working_hours_low','data'=>"Minimum working hours ".$MinimumWorkingHours."min, Your punch_in at ".$sqldata['clock_in'] );
                }

              }

              if($affected_joblist>0)
              {
                return array('code' => 'success','data'=>$timesheetResult );
              }
             return array('code' => 'punch_in_error','data'=>'Punch in time not found' );
          }

          function VerifiedPunchOut($outtime,$timesheet_id,$userid)
          {
            // get job details
            global $db,$MinimumWorkingHours,$BeforePunchIn,$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,$PublicHolidayOTSalary,$MaximumOTHoursPerMonth,$MaximumWorkingHoursPerDay;
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

          function TimesheetSetHoliday($request)
          {
            // get job details
            global $db;
            $DBC=$db::dbconnect();
            $request_data['timesheet_id']=$request['timesheet_id'];
            $TimesheetData=model_timesheet::GetTimeSheet($request_data);
            if($TimesheetData[0]['holiday'] && $TimesheetData[0]['holiday']!='P')
            {
              $sql1 = $DBC->prepare("UPDATE `time_sheet` SET `holiday`=?, `holiday_changed_by`=? WHERE  `id`=?");
              $sql1->bind_param("sii", $request['status'],$request['uid'],$request['timesheet_id']);
              $sql1->execute();
              $affected_Sheet=$sql1->affected_rows;

            }
            return $affected_Sheet;
          }

          function TimesheetSetNotes($id,$notes,$uesrid,$companyid=0)
          {
            global $db;
            $DBC=$db::dbconnect();
            if($companyid>0)
            {
              $sql1 = $DBC->prepare("UPDATE `time_sheet` INNER JOIN  job_list ON job_list.id=time_sheet.job_id
                                      SET `notes`=?, `note_by`=?
                                      WHERE time_sheet.`id`=? AND job_list.employer_id=?");
              $sql1->bind_param("siii", $notes,$uesrid,$id,$companyid);
            }
            else {
              $sql1 = $DBC->prepare("UPDATE `time_sheet` SET `notes`=?, `note_by`=?
                                      WHERE time_sheet.`id`=?");
              $sql1->bind_param("sii", $notes,$uesrid,$id);
            }

            $sql1->execute();
            $affected_Sheet=$sql1->affected_rows;
            return $affected_Sheet;
          }

          function JobseekerLateInfo($id,$lateInfo,$jobseekerid)
          {
            global $db;
            $DBC=$db::dbconnect();
            $sql1 = $DBC->prepare("UPDATE `time_sheet` SET `late_info`=? WHERE `id`=? AND jobseeker_id=?");
            $sql1->bind_param("sii", $lateInfo,$id,$jobseekerid);
            $sql1->execute();
            $affected_Sheet=$sql1->affected_rows;
            return $affected_Sheet;
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
            $sql=$DBC->prepare("INSERT INTO `time_sheet` (`contracts_id`,`job_id`, `jobseeker_id`, `date`, `day`, `holiday` ) VALUES (?, ?, ?, ?, ?, ?)");
            $sql->bind_param("iiisss", $contracts_id, $jobid, $jobseekerid, $date, $day, $is_holiday);
            $sql->execute();
            $insertId=$sql->insert_id;
            return $insertId;
          }

          function TimesheetSalaryGen($id)
          {

          }

}


 ?>
