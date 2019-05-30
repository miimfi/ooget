<?php
class model_faq
{
  function CreateFaq($H_name,$H_date)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `faq` (`name`, `date`) VALUES (?, ?)");
    $sql->bind_param("ss", $H_name,$H_date);
    $sql->execute();
    $insertId=$sql->insert_id;
    return $insertId;
  }

  function DeleteFaq($id)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("DELETE FROM `faq` WHERE  `id`=?");
    $sql->bind_param("i", $id);
    $sql->execute();
    return $sql->affected_rows;
  }

  function GetFaq($request)
  {
    global $db;
    //if($request['id'] || $request['date'] || $request['name'])
    $DBC=$db::dbconnect();
    if($request['id'])
    {
      $sql = $DBC->prepare("SELECT * FROM `faq` where `id`=? ");
      $sql->bind_param("i", $request['id']);
    }
    else if($request['date'])
    {
    //  echo $request['date'];
      $sql = $DBC->prepare("SELECT * FROM `faq` where `date`=? ");
      $sql->bind_param("s", $request['date']);
    }
    else if($request['name'])
    {
      $sql = $DBC->prepare("SELECT * FROM `faq` where `name`=? ");
      $sql->bind_param("s", $request['name']);
    }
    else {
      return 'no data';
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

  function UpdateFaq($request)
  {
    global $db;
    $DBC=$db::dbconnect();
    if($request['id'])
    {
      $sql = $DBC->prepare("SELECT * FROM `faq` WHERE id=?");
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
      $sql=$DBC->prepare("UPDATE `faq` SET `name`=?, `date`=? WHERE  `id`=?");
      $sql->bind_param("ssi", $request['name'], $request['date'], $request['id']);
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

 ?>
