<?php

class model_Employer
{
  function CreateEmployer()
  {
    include_once('model/User.php');
    global $CurrentUser,$request,$db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `company` (`name`, `profile`,`uen`,`companycode`,`industry`,`country`,`createby`) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssssiss",$request['companyname'],$request['profile'],$request['uen'],$request['companycode'],$request['industry'],$request['country'],$CurrentUser->id);
    $sql->execute();
    $insertId=$sql->insert_id;
    //$request['name'],$request['email'],$request['password'],$request['type'],$CurrentUser->id,$request['role'],$request['companyid']
    $userdetails= array('name'=>$request['username'],'email'=>$request['useremail'],'password'=>$request['password'],'type'=>'employer','companyid'=>$insertId);
    $userInsertId=model_user::CreateUser($userdetails);

    return $insertId;

  }

  function CheckCompanyExist($uen)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `uen` FROM company WHERE uen=?");
    $sql->bind_param("s", $uen);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    return $sqldata;
  }

  function CheckCompanyCodeExist($companycode)
  {
    global $request,$db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT `companycode` FROM company WHERE companycode=?");
    $sql->bind_param("s", $companycode);
    $sql->execute();
    $sqldata =$sql->get_result()->fetch_assoc();
    return $sqldata;
  }

  function UpdateEmployer()
  {
    global $db,$request;
    $getdetail= model_Employer::GetEmployer($request['employerid']);
    foreach ($getdetail[0] as $key => $value) {
      if(!array_key_exists($key,$request))
      {
        $request[$key]=$value;
      }
    }
    if(is_array($getdetail))
    {
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("UPDATE `company` SET `name`=?, `profile`=?, `uen`=?, `companycode`=?, `industry`=?, `country`=?, `city`=?, `address1`=?, `address2`=?, `website`=?, `email`=?, `phone`=?, `imgpath`=?, `status`=?, `log`=?, `lat`=? WHERE  `id`=?");
    $sql->bind_param("ssssissssssssissi", $request['name'], $request['profile'], $request['uen'], $request['companycode'], $request['industry'], $request['country'], $request['city'], $request['address1'], $request['address2'], $request['website'], $request['email'], $request['phone'], $request['imgpath'], $request['status'], $request['log'], $request['lat'],$request['employerid']);
    $sql->execute();
    return "updated";
    }
    else {
      return "id not found";
    }
  }

  function DeleteEmployer($id)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("DELETE FROM `company` WHERE  `id`=?");
    $sql->bind_param("i", $id);
    $sql->execute();
    return $sql->affected_rows;
  }

  function FindEmployer($data)
  {

    global $db;
    $data='%'.$data.'%';
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT * FROM `company` WHERE name LIKE? or uen LIKE ? or companycode LIKE ? or city LIKE ? or country LIKE ? or email LIKE ?");
    $sql->bind_param("ssssss", $data,$data,$data,$data,$data,$data);
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
    function GetEmployer($id=0)
    {
      global $db;
      $DBC=$db::dbconnect();
      if(!is_numeric($id))
      {
        return false;
      }
      if($id>0)
      {
        $sql = $DBC->prepare("SELECT company.*, industry.`name` AS `industryname`   FROM `company` INNER JOIN industry ON industry.id=company.industry WHERE company.id=?");
        $sql->bind_param("i", $id);
      }
      else {
        $sql = $DBC->prepare("SELECT company.*, industry.`name` AS `industryname`   FROM `company` INNER JOIN industry ON industry.id=company.industry");
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

}
