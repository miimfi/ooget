<?php
class model_Jobseeker
{
  function CreateJobseeker()
  {
    global $request, $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `jobseeker` (`firstname`, `email`, `password`, `country`) VALUES (?, ?, ?, ?)");
    $sql->bind_param("ssss",$request['name'],$request['email'],$request['password'],$request['country']);
    $sql->execute();
    $insertId=$sql->insert_id;
    return $insertId;
  }

  function CheckEmail($email)
  {
    global $request;
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `email` FROM jobseeker WHERE email =?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    return $sqldata;
  }

  function Login()
  {
    global $request;
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `id`, `firstname`, `lastname`, `email`, `lastlogin`, `status` FROM jobseeker WHERE email =? and password =? ");
    $sql->bind_param("ss", $request['email'],$request['password']);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    if($sqldata['id'])
    {
      $date = date('Y-m-d H:i:s');
      $sql2 = $DBC->prepare("UPDATE `jobseeker` SET `lastlogin`=? WHERE  `id`=?");
      $sql2->bind_param("si", $date,$sqldata['id']);
      $sql2->execute();
    }
    return $sqldata;
  }

  function GetJobseeker($id=0)
  {
    global $db;
    $DBC=$db::dbconnect();
    if($id>0)
    {
      $sql = $DBC->prepare("SELECT * FROM `jobseeker` WHERE id=?");
      $sql->bind_param("i", $id);
    }
    else {
      $sql = $DBC->prepare("SELECT `id`, `imgpath`, `firstname`, `lastname`, `email`, `status`, `country`, `phone`, `gender` FROM `jobseeker`");
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

  function DeleteJobseeker($id)
  {

    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("DELETE FROM `jobseeker` WHERE  `id`=?");
    $sql->bind_param("i", $id);
    $sql->execute();
    return $sql->affected_rows;
  }

  function FindJobseeker($data)
  {
    global $db;
    $DBC=$db::dbconnect();
    $data='%'.$data.'%';
    $sql = $DBC->prepare("SELECT `id`, `imgpath`, `firstname`, `lastname`, `email`, `status`, `country`, `phone`, `gender` FROM `jobseeker` WHERE `firstname` LIKE ? or `lastname` LIKE ? or `email` LIKE ? or `country` LIKE ? or `city` LIKE ?");
    $sql->bind_param("sssss", $data, $data, $data, $data, $data);
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

  function UpdateJobseekerStatus($id,$status)
  {
    global $db;
    $DBC=$db::dbconnect();
    if($id)
    {
      $sql=$DBC->prepare("UPDATE `jobseeker` SET `status`=? WHERE  `id`=?");
      $sql->bind_param("ii", $status,$id);
      $sql->execute();
      echo $sql->affected_rows;
      return $sql->affected_rows;
    }
    else {
      return "id not found";
    }

  }

  function Imagepathupdate($id,$ImagePath)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql2 = $DBC->prepare("UPDATE `jobseeker` SET `imgpath`=? WHERE  `id`=?");
    $sql2->bind_param("si", $ImagePath,$id);
    $sql2->execute();
    return $sql->affected_rows;
  }

  function UpdateJobseeker()
  {
    global $db,$request;
    $DBC=$db::dbconnect();
      if($request['jobseekerid'])
    {
      $sql = $DBC->prepare("SELECT * FROM `jobseeker` WHERE id=?");
      $sql->bind_param("i", $request['jobseekerid']);
      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;
      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          $getdetail[] = $row;
        }
      }

      foreach ($getdetail[0] as $key => $value) {
        if(!array_key_exists($key,$request))
        {
          $request[$key]=$value;
        }
      }

      if(is_array($getdetail))
      {
      $DBC=$db::dbconnect();
      $sql=$DBC->prepare("UPDATE `jobseeker` SET `firstname`=?, `lastname`=?, `password`=?, `country`=?, `dob`=?, `timezone`=?, `phone`=?, `mobile`=?, `address`=?, `city`=?, `status`=?, `bank_id`=?, `branch_code`=?, `account_no`=?, `experience_in`=?, `experience_year`=?, `experience_details`=?, `gender`=?, `nric`=?, `race`=?, `nationality`=?, `employment_type`=?, `region`=?, `location`=?, `notification`=?, `notification_off_from`=?, `notification_off_to`=?, `specializations`=?, `working_environment`=? WHERE  `id`=?");
      $sql->bind_param("ssssssssssiisssisisiisssissssi", $request['firstname'], $request['lastname'], $request['password'], $request['country'], $request['dob'], $request['timezone'], $request['phone'], $request['mobile'], $request['address'], $request['city'], $request['status'], $request['bank_id'], $request['branch_code'], $request['account_no'], $request['experience_in'], $request['experience_year'], $request['experience_details'],$request['gender'], $request['nric'], $request['race'], $request['nationality'], $request['employment_type'], $request['region'], $request['location'], $request['notification'], $request['notification_off_from'], $request['notification_off_to'], $request['specializations'], $request['working_environment'], $request['jobseekerid']);
      $sql->execute();
      return $sql->affected_rows;
      }
      else {
        return "id not found";
      }

    }
    else {
      return "id not found";
    }
  }

}
