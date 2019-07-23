<?php
class model_user
{
  function Login()
  {
    global $request;
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `id`, `firstname`, `lastname`, `type`  as access, `lastlogin`, `status`, `imgpath`,`email`, `role`, `companyid`, `city`, `state`, `country` FROM users WHERE email =? and password =? ");
    $sql->bind_param("ss", $request['email'],$request['password']);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    if($sqldata['id'])
    {
      $date = date('Y-m-d H:i:s');
      $sql2 = $DBC->prepare("UPDATE `users` SET `lastlogin`=? WHERE  `id`=?");
      $sql2->bind_param("si", $date,$sqldata['id']);
      $sql2->execute();
    }
    return $sqldata;
  }

  function UpdatePassword($user_id,$old_pass,$new_pass)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("UPDATE `users` SET `password`=? WHERE  `id`=? AND `password`=?");
    $sql->bind_param("sis", $new_pass,$user_id,$old_pass);
    $sql->execute();
    return $sql->affected_rows;
  }

  function DeleteUser($id,$companyid='')
  {
    global $db;
    $DBC=$db::dbconnect();
    if($companyid>0)
    {
      $sql=$DBC->prepare("DELETE FROM `users` WHERE  `id`=? and `companyid`=?");
      $sql->bind_param("ii", $id,$companyid);
    }
    else {
      $sql=$DBC->prepare("DELETE FROM `users` WHERE  `id`=?");
      $sql->bind_param("i", $id);
    }
    $sql->execute();
    return $sql->affected_rows;
  }

  function CreateUser($request)
  {
    global $CurrentUser;
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `users` (`firstname`, `email`,`password`,`type`,`createdby`,`role`,`companyid`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssssisi",$request['name'],$request['email'],$request['password'],$request['type'],$CurrentUser->id,$request['role'],$request['companyid']);
    $sql->execute();
    $insertId=$sql->insert_id;
    return $insertId;
  }

  function Imagepathupdate($id,$ImagePath)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql2 = $DBC->prepare("UPDATE `users` SET `imgpath`=? WHERE  `id`=?");
    $sql2->bind_param("si", $ImagePath,$id);
    $sql2->execute();
    return $sql2->affected_rows;
  }

  function ForgotPassword($email)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `id`,`firstname`,`lastname`,`email`,`status`,`lastlogin`  FROM `users` WHERE `status`=1 AND `email`=?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $result = $sql->get_result();
    $num_of_rows = $result->num_rows;
    if($num_of_rows>0)
    {
      while($row = $result->fetch_assoc()) {
        $sqldata = $row;
      }
    }
    $key=model_User::randomKey();
    $sql = $DBC->prepare("UPDATE `users` SET `forgot_password_key`=? WHERE  `id`=?");
    $sql->bind_param("ss", $key,$sqldata['id']);
    $sql->execute();
    if($sql->affected_rows>0)
    {
      $sqldata['key']=$key;
      return $sqldata;
    }
    else {
      return false;
    }

  }

  function ForgotPasswordUpdate($Key,$EmailId,$Password)
  {
    global $db;
    $DBC=$db::dbconnect();
    $date = date('Y-m-d H:i:s');
    $sql = $DBC->prepare("UPDATE `users` SET `lastlogin`=?, `password`=?, `forgot_password_key`=null WHERE `email`=? AND `forgot_password_key`=?");
    $sql->bind_param("ssss", $date,$Password,$EmailId,$Key);
    $sql->execute();
    return $sql->affected_rows;
  }

  function randomKey() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 20; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
  }

  function CheckEmail($email)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `email` FROM users WHERE email =?");
    $sql->bind_param("s", $email);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    return $sqldata;
  }

  function GetUserList($CompanyId='')
  {
    global $db;
    $DBC=$db::dbconnect();
    if($CompanyId>0)
    {
      $sql = $DBC->prepare("SELECT * FROM `users` WHERE companyid=?");
      $sql->bind_param("i", $CompanyId);
    }
    else {
      $sql = $DBC->prepare("SELECT * FROM `users`");
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

  function GetUser($id='')
  {
    global $db;
    $DBC=$db::dbconnect();
    if($id>0)
    {
      $sql = $DBC->prepare("SELECT * FROM `users` WHERE id=?");
      $sql->bind_param("i", $id);

      $sql->execute();
      $result = $sql->get_result();
      $num_of_rows = $result->num_rows;

      if($num_of_rows>0)
      {
        while($row = $result->fetch_assoc()) {
          unset($row['password']);
          $sqldata = $row;
        }
      }
    }

    return $sqldata;
  }



  // Updated by sivaraj

   function CheckEmailForUpdate($email)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `email` FROM users WHERE email =? AND id != ?");
    $sql->bind_param("si", $email, $user_id);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    return $sqldata;
  }


  function UpdateUser($request)
  {
    global $CurrentUser;
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("UPDATE `users` SET `firstname`=?, `email`=?,`password`=?,`type`=?,`role`=? WHERE `id`=?");
    $sql->bind_param("ssssii",$request['name'],$request['email'],$request['password'],$request['type'],$request['role'],$request['user_id']);
    $sql->execute();        
    return $sql->affected_rows;        
  }
}
 ?>
