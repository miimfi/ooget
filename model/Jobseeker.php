<?php
class model_Jobseeker
{
  function CreateJobseeker()
  {
    global $request, $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `jobseeker` (`firstname`, `email`, `password`, `country`) VALUES (?, ?, ?, ?)");
    $sql->bind_param("ssss",$request['name'],$request['email'],$request['pass'],$request['country']);
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
    $sql = $DBC->prepare("SELECT `id`, `firstname`, `lastname`, `email` FROM jobseeker WHERE email =? and password =? ");
    $sql->bind_param("ss", $request['email'],$request['pass']);
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
      $sql = $DBC->prepare("SELECT * FROM `jobseeker`");
    }

    $sql->execute();
    $result = $sql->get_result();
    $num_of_rows = $result->num_rows;

    if($num_of_rows>0)
    {
      while($row = $result->fetch_assoc()) {
        unset($row['password']);
        $sqldata[] = $row;
      }
    }
    return $sqldata;

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
      $sql=$DBC->prepare("UPDATE `jobseeker` SET `firstname`=?, `lastname`=?, `password`=?, `country`=?, `dob`=?, `timezone`=?, `theme`=?, `phone`=?, `mobile`=?, `address1`=?, `address2`=?, `city`=?, `imgpath`=? WHERE  `id`=?");
      $sql->bind_param("ssssssissssssi", $request['firstname'], $request['lastname'], $request['password'], $request['country'], $request['dob'], $request['timezone'], $request['theme'], $request['phone'], $request['mobile'], $request['address1'], $request['address2'], $request['city'], $request['imgpath'],  $request['jobseekerid']);
      $sql->execute();
      return "updated";
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
