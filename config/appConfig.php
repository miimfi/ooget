<?php
global $Jobseeker_Allow_Module;

$Jobseeker_Allow_Module= array(
  array('module' => 'Job','mode'=>'GetOpenJobList'),
  array('module' => 'Job','mode'=>'JobApply'),
  array('module' => 'Job','mode'=>'GetAppliedList'),
  array('module' => 'Job','mode'=>'JobseekerJobAccept'),
  array('module' => 'Job','mode'=>'GetJobDetails'),
  array('module' => 'Job','mode'=>'SaveJob'),
  array('module' => 'Job','mode'=>'RemoveSavedJob'),
  array('module' => 'Timesheet','mode'=>'GetJobseekerTimeSheet'),
  array('module' => 'Job','mode'=>'GetJobseekerContractList'),
  array('module' => 'Timesheet','mode'=>'GetTodayJobseekerTimeSheet'),
  array('module' => 'Timesheet','mode'=>'PunchIn'),
  array('module' => 'Timesheet','mode'=>'PunchOut'),
  array('module' => 'Job','mode'=>'MatchedJob'),
  array('module' => 'Terms','mode'=>'GetTerms')
);


 ?>
