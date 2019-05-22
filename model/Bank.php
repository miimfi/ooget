<?php
class model_bank
{
  function CreateBank($request)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `BankDetail` (`full_name`, `short_name`, `bank_code`, `hint`) VALUES (?, ?, ?, ?)");
    $sql->bind_param("ssss", $request['full_name'], $request['short_name'], $request['bank_code'], $request['hint']);
    $sql->execute();
    $insertId=$sql->insert_id;
    return $insertId;
  }

  function Deletebank($id)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("DELETE FROM `BankDetail` WHERE  `id`=?");
    $sql->bind_param("i", $id);
    $sql->execute();
    return $sql->affected_rows;
  }

  function GetBankDetails($id)
  {
    global $db;
    //if($request['id'] || $request['date'] || $request['name'])
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT * FROM `BankDetail` where `id`=? ");
    $sql->bind_param("i", $id);
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

  function UpdateBankDetails($request)
  {
    global $db;
    $DBC=$db::dbconnect();
    if($request['id'])
    {
      $sql = $DBC->prepare("SELECT * FROM `BankDetail` WHERE id=?");
      $sql->bind_param("i", $request['id']);
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
      $sql=$DBC->prepare("UPDATE `BankDetail` SET `full_name`=?, `short_name`=?, `bank_code`=?, `hint`=? WHERE  `id`=?");
      $sql->bind_param("ssssi", $request['full_name'], $request['short_name'], $request['bank_code'], $request['hint'], $request['id']);
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

  function GetBankList()
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql = $DBC->prepare("SELECT * FROM `BankDetail`");
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

 ?>
