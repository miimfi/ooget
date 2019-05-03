<?php

class module_Employer
{
  function CreateEmployer()
  {
    global $CurrentUser,$request,$db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `users` (`firstname`, `email`,`password`,`type`,`createdby`,`role`,`companyid`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssssisi",$request['name'],$request['email'],$request['pass'],$request['type'],$CurrentUser->id,$request['role'],$request['companyid']);
    $sql->execute();
    $insertId=$sql->insert_id;
    return $insertId;
  }

  function CheckCompanyCodeExist($companycode)
  {
    global $request;
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `companycode` FROM company WHERE companycode =?");
    $sql->bind_param("s", $companycode);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    return $sqldata;
  }
}
