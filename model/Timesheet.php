<?php
class model_timesheet
{

      function CheckJobseekerTimeSheet($JobseekerId,$jobid)
      {
        return true;
      }

      function GetJobTimesheetList($jobid,$from,$to,$companyid)
      {
        global $db;
        $DBC=$db::dbconnect();
        if($companyid>0)
        {
          $sql=$DBC->prepare("SELECT jobseeker.firstname, time_sheet.job_id, time_sheet.id AS timesheet_id, time_sheet.`date`, time_sheet.clock_in, time_sheet.clock_out, time_sheet.holiday, time_sheet.clock_verified_in,time_sheet.clock_verified_out
                    FROM time_sheet INNER JOIN jobseeker ON jobseeker.id=time_sheet.jobseeker_id INNER JOIN job_list ON job_list.id=time_sheet.job_id
                    WHERE time_sheet.`job_id`=? AND time_sheet.`date` BETWEEN ? AND ? AND job_list.employer_id=?");
          $sql->bind_param("iss", $jobid,$from,$to,$companyid);
        }
        else {
          $sql=$DBC->prepare("SELECT jobseeker.firstname, time_sheet.job_id, time_sheet.id AS timesheet_id, time_sheet.`date`, time_sheet.clock_in, time_sheet.clock_out, time_sheet.holiday, time_sheet.clock_verified_in,time_sheet.clock_verified_out
                    FROM time_sheet INNER JOIN jobseeker ON jobseeker.id=time_sheet.jobseeker_id
                    WHERE time_sheet.`job_id`=? AND time_sheet.`date` BETWEEN ? AND ?");
          $sql->bind_param("iss", $jobid,$from,$to);
        }

        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
          {
              while($row = $result->fetch_assoc()) {
                  if($row['clock_in'] || $row['clock_verified_in'] )
                  {
                    $row['worked']='ON';
                  }
                  else {
                    $row['worked']='OFF';
                  }
                  $sqldata[]= $row;
                }
          }

        return $sqldata;
      }

      function GetEmployerContractTimesheetList($companyid)
      {
        global $db,$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,$PublicHolidayOTSalary;
        $DBC=$db::dbconnect();



        $sql=$DBC->prepare("SELECT time_sheet.*, jobseeker.firstname,  time_sheet.id AS timesheet_id,job_list.job_name, job_list.job_no, job_list.charge_rate,job_list.markup_amount,job_list.jobseeker_salary
                            FROM time_sheet INNER JOIN jobseeker ON jobseeker.id=time_sheet.jobseeker_id INNER JOIN job_list ON job_list.id=time_sheet.job_id
                            WHERE job_list.employer_id=?");
          $sql->bind_param("i", $companyid);

        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
          {
              while($row = $result->fetch_assoc()) {


                    // employer charge_rate calculate
                    if($row['holiday']=="P")
                    {
                      $EmployerCharge=(($row['charge_rate']/60)*$PublicHolidaySalary)*$row['jobseeker_normal_working_min'];
                      $EmployerOTCharge=(($row['charge_rate']/60)*$PublicHolidayOTSalary)*$row['jobseeker_ot_working_min'];
                    }elseif($row['holiday']=="Y"){
                      $EmployerCharge=(($row['charge_rate']/60)*$HolidaySalary)*$row['jobseeker_normal_working_min'];
                      $EmployerOTCharge=(($row['charge_rate']/60)*$HolidayOTSalary)*$row['jobseeker_ot_working_min'];
                    }else {
                      $EmployerCharge=($row['charge_rate']/60)*$row['jobseeker_normal_working_min'];
                      $EmployerOTCharge=(($row['charge_rate']/60)*$OverTimetSalary)*$row['jobseeker_ot_working_min'];
                    }

                    $row['employer_charge']=$EmployerCharge;
                    $row['employer_ot_charge']=$EmployerOTCharge;
                    $row['employer_total_charge']=$EmployerOTCharge+$EmployerCharge;
                    $jobList[$row['job_id']]['job_name']=$row['job_name'];
                    $jobList[$row['job_id']]['job_id']=$row['job_id'];
                    $jobList[$row['job_id']]['job_no']=$row['job_no'];
                    $contract[$row['job_id']][$row['contracts_id']]['timesheet'][]=$row;
                    $contract[$row['job_id']][$row['contracts_id']]['jobseeker_name']=$row['firstname'];
                    $contract[$row['job_id']][$row['contracts_id']]['jobseeker_id']=$row['jobseeker_id'];
                    $contract[$row['job_id']][$row['contracts_id']]['contracts_id']=$row['contracts_id'];

                }
          }

          foreach ($jobList as $key => $value) {
            $rowdata= array();
            $rowdata['job_name']=$value['job_name'];
            $rowdata['job_id']=$value['job_id'];
            $rowdata['job_no']=$value['job_no'];
            foreach ($contract[$rowdata['job_id']] as $key => $value) {
              $rowdata['contract'][]=$value;
            }
            $timelist['jobs'][]=$rowdata;
          }

        return $timelist;
      }

      function GetEmployerContractTimesheetListCom($companyid)
      {
        global $db;
        $DBC=$db::dbconnect();
        $sql=$DBC->prepare("SELECT time_sheet.*, jobseeker.firstname,  time_sheet.id AS timesheet_id,job_list.job_name, job_list.job_no
                            FROM time_sheet INNER JOIN jobseeker ON jobseeker.id=time_sheet.jobseeker_id INNER JOIN job_list ON job_list.id=time_sheet.job_id
                            WHERE job_list.employer_id=?");
          $sql->bind_param("i", $companyid);

        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
          {
              while($row = $result->fetch_assoc()) {
                $TimesheetDataRaw[$row['contracts_id']]['timesheet'][]=$row;
                $TimesheetDataRaw[$row['contracts_id']]['contracts_id']=$row['contracts_id']  ;

              }
          }

          $sql=$DBC->prepare("SELECT job_list.id AS job_id, job_list.job_name,job_list.job_no FROM job_list
                WHERE employer_id=?");
          $sql->bind_param("i", $companyid);
          $sql->execute();
          $result = $sql->get_result();
          $num_of_rows = $result->num_rows;
          if($num_of_rows>0)
            {
                while($row = $result->fetch_assoc()) {
                //  $row['contract']=$row[]
                  $sqldata[]=$row;
                }
            }

        return $sqldata;
      }


      function GetJobContractTimesheetList($jobid,$from,$to,$companyid)
      {
        global $db;
        $DBC=$db::dbconnect();
        if($companyid>0)
        {
          $sql=$DBC->prepare("SELECT time_sheet.contracts_id,jobseeker.firstname, time_sheet.job_id, time_sheet.id AS timesheet_id, time_sheet.`date`, time_sheet.clock_in, time_sheet.clock_out, time_sheet.holiday, time_sheet.clock_verified_in,time_sheet.clock_verified_out
                    FROM time_sheet INNER JOIN jobseeker ON jobseeker.id=time_sheet.jobseeker_id INNER JOIN job_list ON job_list.id=time_sheet.job_id WHERE time_sheet.`job_id`=? AND time_sheet.`date` BETWEEN ? AND ? AND job_list.employer_id=?");
          $sql->bind_param("iss", $jobid,$from,$to,$companyid);
        }
        else {
          $sql=$DBC->prepare("SELECT time_sheet.contracts_id,jobseeker.firstname, time_sheet.job_id, time_sheet.id AS timesheet_id, time_sheet.`date`, time_sheet.clock_in, time_sheet.clock_out, time_sheet.holiday, time_sheet.clock_verified_in,time_sheet.clock_verified_out
                    FROM time_sheet INNER JOIN jobseeker ON jobseeker.id=time_sheet.jobseeker_id WHERE time_sheet.`job_id`=? AND time_sheet.`date` BETWEEN ? AND ?");
          $sql->bind_param("iss", $jobid,$from,$to);
        }

        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
          {
              while($row = $result->fetch_assoc()) {
                  if($row['clock_in'] || $row['clock_verified_in'] )
                  {
                    $row['worked']='ON';
                  }
                  else {
                    $row['worked']='OFF';
                  }
                  $sqldata[$row['contracts_id']][]= $row;
                }
          }

          $timelist;
          foreach ($sqldata as $key => $value) {
            $rr['timesheet']=$sqldata[$key];
            $rr['jobseekername']=$value[0]['firstname'];
            //$temp_data[]=$value;
            $timelist[]=$rr;
          }

        return $timelist;
      }

      function GetJobseekerContractTimesheetList($jobseekerid,$from,$to,$companyid)
      {
        include_once("model/Job.php");
        global $db;
        $DBC=$db::dbconnect();
        if($companyid>0)
        {
          $sql=$DBC->prepare("SELECT time_sheet.*,jobseeker.firstname, time_sheet.id AS timesheet_id,
job_list.`from` AS jobperiodfrom, job_list.`to` AS jobperiodto, job_list.start_time, job_list.end_time,job_list.jobseeker_salary AS job_hour_salary,company.name AS company_name
FROM time_sheet
INNER JOIN jobseeker ON jobseeker.id=time_sheet.jobseeker_id
INNER JOIN job_list ON job_list.id=time_sheet.job_id
INNER JOIN company ON company.id=job_list.employer_id WHERE time_sheet.`jobseeker_id`=? AND time_sheet.`date` BETWEEN ? AND ? AND job_list.employer_id=?");
          $sql->bind_param("iss", $jobseekerid,$from,$to,$companyid);
        }
        else {
          $sql=$DBC->prepare("SELECT time_sheet.*,jobseeker.firstname, time_sheet.id AS timesheet_id,
job_list.`from` AS jobperiodfrom, job_list.`to` AS jobperiodto, job_list.start_time, job_list.end_time,job_list.jobseeker_salary AS job_hour_salary,company.name AS company_name
FROM time_sheet
INNER JOIN jobseeker ON jobseeker.id=time_sheet.jobseeker_id
INNER JOIN job_list ON job_list.id=time_sheet.job_id
INNER JOIN company ON company.id=job_list.employer_id WHERE time_sheet.`jobseeker_id`=? AND time_sheet.`date` BETWEEN ? AND ?");
          $sql->bind_param("iss", $jobseekerid,$from,$to);
        }

        $sql->execute();
        $result = $sql->get_result();
        $num_of_rows = $result->num_rows;
        if($num_of_rows>0)
          {
              while($row = $result->fetch_assoc()) {
                  $sqldata[$row['contracts_id']][]= $row;
                }
          }

          $timelist;
          foreach ($sqldata as $key => $value) {
            $rr['timesheet']=$sqldata[$key];
            $rr['job']['job_id']=$value[0]['job_id'];
            $rr['job']['jobperiodfrom']=$value[0]['jobperiodfrom'];
            $rr['job']['jobperiodto']=$value[0]['jobperiodto'];
            $rr['job']['starttime']=$value[0]['start_time'];
            $rr['job']['endtime']=$value[0]['end_time'];
            $rr['job']['job_salary']=$value[0]['job_hour_salary'];
            $rr['job']['breaktime']=model_Job::GetJobBreakTime($rr['job']['job_id']);
            $rr['companyname']=$value[0]['company_name'];
            $rr['jobseekername']=$value[0]['firstname'];
            //$temp_data[]=$value;
            $timelist[]=$rr;
          }

          //timesheetreport
        return $timelist;
      }

      function GetTimeSheet($request)
      {

        global $db,$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,$PublicHolidayOTSalary;
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
            $row['normal_salary_type']=1;
            $row['ot_salary_type']=$OverTimetSalary;
            if($row['holiday']=='Y')
            {
              $row['normal_salary_type']=$HolidaySalary;
              $row['ot_salary_type']=$HolidayOTSalary;
            }elseif($row['holiday']=='P')
            {
              $row['normal_salary_type']=$PublicHolidaySalary;
              $row['ot_salary_type']=$PublicHolidayOTSalary;
            }
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

            $today=date("Y-m-d");//set job salary based on day type
            $now_today=date("Y-m-d H:i:s");
            $yesterday=date('Y-m-d', strtotime('-1 days'));
            $nextday=date('Y-m-d', strtotime('+1 days'));

            // get job details
            global $db,$MinimumWorkingHours,$BeforePunchIn,$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,$PublicHolidayOTSalary,$WorkingHoursRound;
            $DBC=$db::dbconnect();
            $sql = $DBC->prepare("SELECT time_sheet.job_id, time_sheet.clock_verified_in, time_sheet.holiday, job_list.markup_amount, time_sheet.clock_verified_out, time_sheet.clock_in, time_sheet.clock_out, job_list.start_time, job_list.end_time, time_sheet.DATE, job_list.grace_period,job_list.over_time_rounding,job_list.over_time_minimum,job_list.work_days_type,job_list.jobseeker_salary
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

            // grace_period check
            if($sqldata['grace_period']>0)
            {
              $Job_in_time=$sqldata['DATE'].' '.$sqldata['start_time'];
              $grace_in_time=strtotime($Job_in_time)+($sqldata['grace_period']*60);
              $Check_break_clockin=($sqldata['clock_verified_in']?$sqldata['clock_verified_in']: $sqldata['clock_in']);
              if($grace_in_time>strtotime($Check_break_clockin))
              {
                $sqldata['clock_verified_in']=$Job_in_time;
              }
            }

            $break_min=0;
            $sql_break = $DBC->prepare("SELECT `from`,`to` FROM  job_break_list WHERE `job_id`=? AND `from`<`to`");
            $sql_break->bind_param("i", $sqldata['job_id']);
            $sql_break->execute();
            $result_break = $sql_break->get_result();
            $num_of_rows_break = $result_break->num_rows;
            if($num_of_rows_break>0)
            {
              $Check_break_clockin=($sqldata['clock_verified_in']?$sqldata['clock_verified_in']: $sqldata['clock_in']);
              $Check_break_clockout=$now_today;
              while($row = $result_break->fetch_assoc()) {
                if((strtotime($Check_break_clockin)<strtotime($row['to']) && strtotime($Check_break_clockin)<strtotime($row['from'])) && (strtotime($Check_break_clockout)>strtotime($row['to']) && strtotime($Check_break_clockout)>strtotime($row['from'])))
                {
                  $break_min=$break_min+(strtotime($row['to'])-strtotime($row['from']))/60;
                }
                $break_min_uncount=$break_min_uncount+(strtotime($row['to'])-strtotime($row['from']))/60;
              }
            }




            if($sqldata['clock_verified_out'] || $sqldata['clock_out'])
            {
              $C_status="Already ".($sqldata['clock_verified_out']?"verified : ".$sqldata['clock_verified_out']:"punch out at ".$sqldata['clock_out']);
              return array('code' => ($sqldata['clock_verified_out']?'clock_out_verified':'already_punch_out'),'data'=>$C_status );
            }
            else {

              if(strtotime($now_today)>(strtotime($sqldata['clock_in'])+($MinimumWorkingHours*60)))
              {

                //set job salary based on day type
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

                // without break
                $JobWorkingMin=$JobWorkingMin-$break_min_uncount;
                $TotalWorkingMin=round((strtotime($now_today)-strtotime(($sqldata['clock_verified_in']?$sqldata['clock_verified_in']:$sqldata['clock_in'])))/60);

                // without break
                $TotalWorkingMin=$TotalWorkingMin-$break_min;

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
                  $NormalWorkingMin=$JobWorkingMin;
                  $JobseekerSalary=$NormalWorkingMin*$salaryPerMini;
                }
                else {
                  // below job hours
                  if($WorkingHoursRound)
                  {
                    $normalMinnonecount=0;
                    $normalMinnonecount=$TotalWorkingMin%$WorkingHoursRound;
                    $TotalWorkingMin=$TotalWorkingMin-$normalMinnonecount;
                    $NormalWorkingMin=$TotalWorkingMin;
                  }
                  $JobseekerSalary=$TotalWorkingMin*$salaryPerMini;
                }

                $JobseekerOTSalary=$JobseekerOTmin*$OTsalaryMin;
                $TotalJobSeekerSalary=$JobseekerOTSalary+$JobseekerSalary;

                // ooget commision
                $MarkupAmountPer=($sqldata['markup_amount']/$salary)*100;
                $ooget_commision=($TotalJobSeekerSalary/100)*$MarkupAmountPer;

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
                $timesheetResult['MarkupAmount']=$sqldata['markup_amount'];
                $timesheetResult['ooget_commision']=$ooget_commision;

                $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_out`=?, `ot_salary`=?, `salary`=?, `salary_total`=?, `total_job_min`=?, `jobseeker_normal_working_min`=?, `jobseeker_ot_working_min`=?, `ooget_commision`=? WHERE  `id`=?");
                $sql2->bind_param("sdddiiidi", $now_today,$JobseekerOTSalary,$JobseekerSalary,$TotalJobSeekerSalary,$JobWorkingMin,$NormalWorkingMin,$JobseekerOTmin,$ooget_commision,$timesheet_id);
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
            $today=date("Y-m-d");
            $now_today=$outtime;
            $yesterday=date('Y-m-d', strtotime('-1 days'));
            $nextday=date('Y-m-d', strtotime('+1 days'));

            // get job details
            global $db,$MinimumWorkingHours,$BeforePunchIn,$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,$PublicHolidayOTSalary,$WorkingHoursRound;
            $DBC=$db::dbconnect();
            $sql = $DBC->prepare("SELECT time_sheet.job_id,
              time_sheet.clock_verified_in, time_sheet.holiday, job_list.markup_amount, time_sheet.clock_verified_out, time_sheet.clock_in, time_sheet.clock_out, job_list.start_time, job_list.end_time, time_sheet.DATE, job_list.grace_period,job_list.over_time_rounding,job_list.over_time_minimum,job_list.work_days_type,job_list.jobseeker_salary
              FROM time_sheet INNER JOIN contracts ON contracts.id = time_sheet.contracts_id INNER JOIN job_list ON job_list.id = contracts.job_id
              WHERE time_sheet.`id`=? AND (time_sheet.`clock_in` IS NOT NULL OR time_sheet.`clock_verified_in` IS NOT NULL)");
            $sql->bind_param("i", $timesheet_id);
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

            // grace_period check
            if($sqldata['grace_period']>0)
            {
              $Job_in_time=$sqldata['DATE'].' '.$sqldata['start_time'];
              $grace_in_time=strtotime($Job_in_time)+($sqldata['grace_period']*60);
              $Check_break_clockin=($sqldata['clock_verified_in']?$sqldata['clock_verified_in']: $sqldata['clock_in']);
              if($grace_in_time>strtotime($Check_break_clockin))
              {
                $sqldata['clock_verified_in']=$Job_in_time;
              }
            }

            $break_min=0;
            $sql_break = $DBC->prepare("SELECT `from`,`to` FROM  job_break_list WHERE `job_id`=? AND `from`<`to`");
            $sql_break->bind_param("i", $sqldata['job_id']);
            $sql_break->execute();
            $result_break = $sql_break->get_result();
            $num_of_rows_break = $result_break->num_rows;
            if($num_of_rows_break>0)
            {
              $Check_break_clockin=($sqldata['clock_verified_in']?$sqldata['clock_verified_in']: $sqldata['clock_in']);
              $Check_break_clockout=$now_today;
              while($row = $result_break->fetch_assoc()) {
                if((strtotime($Check_break_clockin)<strtotime($row['to']) && strtotime($Check_break_clockin)<strtotime($row['from'])) && (strtotime($Check_break_clockout)>strtotime($row['to']) && strtotime($Check_break_clockout)>strtotime($row['from'])))
                {
                  $break_min=$break_min+(strtotime($row['to'])-strtotime($row['from']))/60;
                }
                $break_min_uncount=$break_min_uncount+(strtotime($row['to'])-strtotime($row['from']))/60;
              }
            }

                //set job salary based on day type
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

                // without break
                $JobWorkingMin=$JobWorkingMin-$break_min_uncount;

                $TotalWorkingMin=round((strtotime($now_today)-strtotime(($sqldata['clock_verified_in']?$sqldata['clock_verified_in']:$sqldata['clock_in'])))/60);

                // without break
                $TotalWorkingMin=$TotalWorkingMin-$break_min;

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
                  $NormalWorkingMin=$JobWorkingMin;
                  $JobseekerSalary=$NormalWorkingMin*$salaryPerMini;
                }
                else {
                  // below job hours
                  if($WorkingHoursRound)
                  {
                    $normalMinnonecount=0;
                    $normalMinnonecount=$TotalWorkingMin%$WorkingHoursRound;
                    $TotalWorkingMin=$TotalWorkingMin-$normalMinnonecount;
                    $NormalWorkingMin=$TotalWorkingMin;
                  }
                  $JobseekerSalary=$TotalWorkingMin*$salaryPerMini;
                }

                $JobseekerOTSalary=$JobseekerOTmin*$OTsalaryMin;
                $TotalJobSeekerSalary=$JobseekerOTSalary+$JobseekerSalary;

                // ooget commision
                $MarkupAmountPer=($sqldata['markup_amount']/($salary+$sqldata['markup_amount']))*100;
                $salaryPer=100-$MarkupAmountPer;
                $salary1per=$TotalJobSeekerSalary/$salaryPer;
                $ooget_commision=$salary1per*$MarkupAmountPer;
                $EmployerCharge=$ooget_commision+$TotalJobSeekerSalary;

                $timesheetResult['PunchOut']=$now_today;
                $timesheetResult['Salary']=$salary;
                $timesheetResult['SlaryPerMini']=$salaryPerMini;
                $timesheetResult['OTslaryMini']=$OTsalaryMin;
                $timesheetResult['TotalWorkingmin']=$TotalWorkingMin;
                $timesheetResult['NormalWorkingmin']=$NormalWorkingMin;
                $timesheetResult['JobWorkingMin']=$JobWorkingMin;
                $timesheetResult['OTTimeMin']=$JobseekerOTmin;
                $timesheetResult['JobseekerSalary']=$JobseekerSalary;
                $timesheetResult['JobseekerOTSalary']=$JobseekerOTSalary;
                $timesheetResult['TotalJobSeekerSalary']=$TotalJobSeekerSalary;
                $timesheetResult['MarkupAmount']=$sqldata['markup_amount'];
                $timesheetResult['ooget_commision']=$ooget_commision;
                $timesheetResult['EmployerCharge']=$EmployerCharge;

                $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_verified_out`=?, `ot_salary`=?, `salary`=?, `salary_total`=?,`clock_out_verified_by`=?, `total_job_min`=?, `jobseeker_normal_working_min`=?, `jobseeker_ot_working_min`=?, `ooget_commision`=? WHERE  `id`=?");
                $sql2->bind_param("sdddiiiidi", $now_today,$JobseekerOTSalary,$JobseekerSalary,$TotalJobSeekerSalary,$userid,$JobWorkingMin,$NormalWorkingMin,$JobseekerOTmin,$ooget_commision,$timesheet_id);
                $sql2->execute();
                $affected_joblist=$sql2->affected_rows;
              if($affected_joblist>0)
              {
                return array('code' => 'success','data'=>$timesheetResult );
              }else {
                return array('code' => 'Update_error','data'=>'server error' );
              }

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

          function VerifiedTimesheet($id,$uesrid,$companyid=0)
          {
            global $db;
            $now_today=date("Y-m-d H:i:s");
            $DBC=$db::dbconnect();

            if($companyid>0)
            {
              $sql1 = $DBC->prepare("UPDATE `time_sheet` INNER JOIN  job_list ON job_list.id=time_sheet.job_id
                                      SET `sheet_verified`=?, `sheet_verified_by`=?
                                      WHERE time_sheet.`id` IN (".$id.") AND job_list.employer_id=? AND (time_sheet.`clock_in` IS NOT NULL OR time_sheet.`clock_verified_in` IS NOT NULL) AND (time_sheet.`clock_out` IS NOT NULL OR time_sheet.`clock_verified_out` IS NOT NULL)");
              $sql1->bind_param("sii", $now_today,$uesrid,$companyid);
            }
            else {
              $sql1 = $DBC->prepare("UPDATE `time_sheet` SET `sheet_verified`=?, `sheet_verified_by`=?
                                      WHERE time_sheet.`id` IN (".$id.") AND (time_sheet.`clock_in` IS NOT NULL OR time_sheet.`clock_verified_in` IS NOT NULL) AND (time_sheet.`clock_out` IS NOT NULL OR time_sheet.`clock_verified_out` IS NOT NULL)");
              $sql1->bind_param("si", $now_today,$uesrid);
            }

            $sql1->execute();
            $affected_Sheet=$sql1->affected_rows;
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
                                      WHERE time_sheet.`id`=?  AND job_list.employer_id=?");
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
            $today=date("Y-m-d");
            $now_today=$outtime;
            $yesterday=date('Y-m-d', strtotime('-1 days'));
            $nextday=date('Y-m-d', strtotime('+1 days'));

            // get job details
            global $db,$MinimumWorkingHours,$BeforePunchIn,$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,$PublicHolidayOTSalary,$WorkingHoursRound;
            $DBC=$db::dbconnect();
            $sql = $DBC->prepare("SELECT time_sheet.job_id,
              time_sheet.clock_verified_in, time_sheet.holiday, job_list.markup_amount, time_sheet.clock_verified_out, time_sheet.clock_in, time_sheet.clock_out, job_list.start_time, job_list.end_time, time_sheet.DATE, job_list.grace_period,job_list.over_time_rounding,job_list.over_time_minimum,job_list.work_days_type,job_list.jobseeker_salary
              FROM time_sheet INNER JOIN contracts ON contracts.id = time_sheet.contracts_id INNER JOIN job_list ON job_list.id = contracts.job_id
              WHERE time_sheet.`id`=? AND (time_sheet.`clock_in` IS NOT NULL OR time_sheet.`clock_verified_in` IS NOT NULL)");
            $sql->bind_param("i", $timesheet_id);
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

            // grace_period check
            if($sqldata['grace_period']>0)
            {
              $Job_in_time=$sqldata['DATE'].' '.$sqldata['start_time'];
              $grace_in_time=strtotime($Job_in_time)+($sqldata['grace_period']*60);
              $Check_break_clockin=($sqldata['clock_verified_in']?$sqldata['clock_verified_in']: $sqldata['clock_in']);
              if($grace_in_time>strtotime($Check_break_clockin))
              {
                $sqldata['clock_verified_in']=$Job_in_time;
              }
            }

            $break_min=0;
            $sql_break = $DBC->prepare("SELECT `from`,`to` FROM  job_break_list WHERE `job_id`=? AND `from`<`to`");
            $sql_break->bind_param("i", $sqldata['job_id']);
            $sql_break->execute();
            $result_break = $sql_break->get_result();
            $num_of_rows_break = $result_break->num_rows;
            if($num_of_rows_break>0)
            {
              $Check_break_clockin=($sqldata['clock_verified_in']?$sqldata['clock_verified_in']: $sqldata['clock_in']);
              $Check_break_clockout=$now_today;
              while($row = $result_break->fetch_assoc()) {
                if((strtotime($Check_break_clockin)<strtotime($row['to']) && strtotime($Check_break_clockin)<strtotime($row['from'])) && (strtotime($Check_break_clockout)>strtotime($row['to']) && strtotime($Check_break_clockout)>strtotime($row['from'])))
                {
                  $break_min=$break_min+(strtotime($row['to'])-strtotime($row['from']))/60;
                }
                $break_min_uncount=$break_min_uncount+(strtotime($row['to'])-strtotime($row['from']))/60;
              }
            }

                //set job salary based on day type
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

                // without break
                $JobWorkingMin=$JobWorkingMin-$break_min_uncount;

                $TotalWorkingMin=round((strtotime($now_today)-strtotime(($sqldata['clock_verified_in']?$sqldata['clock_verified_in']:$sqldata['clock_in'])))/60);

                // without break
                $TotalWorkingMin=$TotalWorkingMin-$break_min;

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
                  $NormalWorkingMin=$JobWorkingMin;
                  $JobseekerSalary=$NormalWorkingMin*$salaryPerMini;
                }
                else {
                  // below job hours
                  if($WorkingHoursRound)
                  {
                    $normalMinnonecount=0;
                    $normalMinnonecount=$TotalWorkingMin%$WorkingHoursRound;
                    $TotalWorkingMin=$TotalWorkingMin-$normalMinnonecount;
                    $NormalWorkingMin=$TotalWorkingMin;
                  }
                  $JobseekerSalary=$TotalWorkingMin*$salaryPerMini;
                }

                $JobseekerOTSalary=$JobseekerOTmin*$OTsalaryMin;
                $TotalJobSeekerSalary=$JobseekerOTSalary+$JobseekerSalary;

                // ooget commision
                $MarkupAmountPer=($sqldata['markup_amount']/($salary+$sqldata['markup_amount']))*100;
                $salaryPer=100-$MarkupAmountPer;
                $salary1per=$TotalJobSeekerSalary/$salaryPer;
                $ooget_commision=$salary1per*$MarkupAmountPer;
                $EmployerCharge=$ooget_commision+$TotalJobSeekerSalary;

                $timesheetResult['PunchOut']=$now_today;
                $timesheetResult['Salary']=$salary;
                $timesheetResult['SlaryPerMini']=$salaryPerMini;
                $timesheetResult['OTslaryMini']=$OTsalaryMin;
                $timesheetResult['TotalWorkingmin']=$TotalWorkingMin;
                $timesheetResult['NormalWorkingmin']=$NormalWorkingMin;
                $timesheetResult['JobWorkingMin']=$JobWorkingMin;
                $timesheetResult['OTTimeMin']=$JobseekerOTmin;
                $timesheetResult['JobseekerSalary']=$JobseekerSalary;
                $timesheetResult['JobseekerOTSalary']=$JobseekerOTSalary;
                $timesheetResult['TotalJobSeekerSalary']=$TotalJobSeekerSalary;
                $timesheetResult['MarkupAmount']=$sqldata['markup_amount'];
                $timesheetResult['ooget_commision']=$ooget_commision;
                $timesheetResult['EmployerCharge']=$EmployerCharge;

                $sql2 = $DBC->prepare("UPDATE `time_sheet` SET `clock_verified_out`=?, `ot_salary`=?, `salary`=?, `salary_total`=?,`clock_out_verified_by`=?, `total_job_min`=?, `jobseeker_normal_working_min`=?, `jobseeker_ot_working_min`=?, `ooget_commision`=? WHERE  `id`=?");
                $sql2->bind_param("sdddiiiidi", $now_today,$JobseekerOTSalary,$JobseekerSalary,$TotalJobSeekerSalary,$userid,$JobWorkingMin,$NormalWorkingMin,$JobseekerOTmin,$ooget_commision,$timesheet_id);
                $sql2->execute();
                $affected_joblist=$sql2->affected_rows;
              if($affected_joblist>0)
              {
                return array('code' => 'success','data'=>$timesheetResult );
              }else {
                return array('code' => 'Update_error','data'=>'server error' );
              }
          }

}


 ?>
