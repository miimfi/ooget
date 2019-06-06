<?php
global $Jobseeker_Allow_Module,$BeforePunchIn,$MinimumWorkingHours,$HolidaySalary,$PublicHolidaySalary,$OverTimetSalary,$HolidayOTSalary,$PublicHolidayOTSalary,$MaximumOTHoursPerMonth,$MaximumWorkingHoursPerDay,$WorkingHoursRound,$MinimumOTHours;

// set allow to jobseeker to bunch befor clock_in time (min)
$BeforePunchIn=15;

// set minimum punch out time in minits (jobseeker can't punch_out before clock_in + $MinimumWorkingHours )
$MinimumWorkingHours=1; // it will be affect when jobseeker working hours is below job hours
$MinimumOTHours=20;

/* working hours round in minits
(its avoid none round of time on salary calculation)
eg:- jobseeker working hours = 28min, $WorkingHoursRound=10min, salary calculation time= 20min, 8min not Countable */
$WorkingHoursRound=1;

//salary = job_salary * $HolidaySalary (15=10*1.5)
$HolidaySalary=1.5;

//salary = job_salary * $PublicHolidaySalary (20=10*2)
$PublicHolidaySalary=2;

//salary = job_salary * $OverTimetSalary (15=10*1.5) - normal working days
$OverTimetSalary=1.5;
$HolidayOTSalary=1.5;
$PublicHolidayOTSalary=2;
$MaximumOTHoursPerMonth=0; // Under the Employment Act, overtime not more than 72 hours a month.
$MaximumWorkingHoursPerDay=0; // Under the Employment Act, maximum working hors not more than 12 hours a day.

//invoice GST in %
$invoiceGST=7;

$Jobseeker_Allow_Module= array(
  array('module' => 'Job','mode'=>'GetOpenJobList'),
  array('module' => 'Job','mode'=>'JobApply'),
  array('module' => 'Job','mode'=>'GetAppliedList'),
  array('module' => 'Job','mode'=>'JobseekerJobAccept'),
  array('module' => 'Job','mode'=>'GetJobDetails'),
  array('module' => 'Job','mode'=>'SaveJob'),
  array('module' => 'Job','mode'=>'RemoveSavedJob'),
  array('module' => 'Timesheet','mode'=>'GetTimeSheet'),
  array('module' => 'Job','mode'=>'GetJobseekerContractList'),
  array('module' => 'Timesheet','mode'=>'GetTodayJobseekerTimeSheet'),
  array('module' => 'Timesheet','mode'=>'PunchIn'),
  array('module' => 'Timesheet','mode'=>'PunchOut'),
  array('module' => 'Timesheet','mode'=>'JobseekerLateInfo'),
  array('module' => 'Job','mode'=>'MatchedJob'),
  array('module' => 'Terms','mode'=>'GetTerms')
);




 ?>
