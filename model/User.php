<?php
class model_user
{
  function UserAuthentication()
  {
    global $request;
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `id`, `firstname`, `lastname`, `email`, `role`, `companyid`, `city`, `state`, `country` FROM users WHERE email =? and password =? ");
    $sql->bind_param("ss", $request['email'],$request['pass']);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    if($sqldata['id'])
    {
      echo $date = date('Y-m-d H:i:s');
      $sql2 = $DBC->prepare("UPDATE `users` SET `lastlogin`=? WHERE  `id`=?");
      $sql2->bind_param("si", $date,$sqldata['id']);
      $sql2->execute();
    }
    return $sqldata;
  }
  function UpdateAdmin()
  {

  }

  function CreateUser()
  {
    global $CurrentUser,$request;
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `users` (`firstname`, `email`,`password`,`type`,`createdby`,`role`,`companyid`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssssisi",$request['name'],$request['email'],$request['pass'],$request['type'],$CurrentUser->id,$request['role'],$request['companyid']);
    $sql->execute();
    $insertId=$sql->insert_id;
    return $insertId;
  }

  function CheckEmail($email)
  {
    global $request;
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `email` FROM users WHERE email =?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    return $sqldata;
  }

  function CreateCompany()
  {

  }

}
 ?>
