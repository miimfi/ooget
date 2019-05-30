<?php
class model_terms
{
  function CreateTerms($request)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("INSERT INTO `terms` (`ref_id`, `title`, `body`, `type`) VALUES (?, ?, ?, ?)");
    $sql->bind_param("issi", $request['ref_id'], $request['title'], $request['body'], $request['type']);
    $sql->execute();
    $insertId=$sql->insert_id;
    return $insertId;
  }

  function DeleteTerms($id)
  {
    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("DELETE FROM `terms` WHERE  `id`=? and ref_id!=0");
    $sql->bind_param("i", $id);
    $sql->execute();
    return $sql->affected_rows;
  }

  function GetTerms($id,$ref_id,$type)
  {
    global $db;
    if(!$ref_id)
    {
      $ref_id=0;
    }
    $DBC=$db::dbconnect();
    if($id)
    {

      $sql = $DBC->prepare("SELECT * FROM `terms` where `id`=? ");
      $sql->bind_param("i", $id);
    }
    else
    {
      $sql = $DBC->prepare("SELECT * FROM `terms` where `ref_id`=? and `type`=?");
      $sql->bind_param("ii", $ref_id,$type);
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

  function UpdateTerms($request)
  {

    global $db;
    $DBC=$db::dbconnect();
    $sql=$DBC->prepare("UPDATE `terms` SET `title`=?, `body`=? WHERE  `id`=?");
    $sql->bind_param("ssi", $request['title'], $request['body'], $request['id']);
    $sql->execute();
    $affected_joblist=$sql->affected_rows;
    return $affected_joblist;
  }


    function GetTermsList($type=0)
    {
      global $db;
      $DBC=$db::dbconnect();
      if($type>0)
      {
        $sql = $DBC->prepare("SELECT * FROM `terms` WHERE `type`=?");
        $sql->bind_param('i', $type);
      }
      else {
        $sql = $DBC->prepare("SELECT * FROM `terms`");
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

 ?>
