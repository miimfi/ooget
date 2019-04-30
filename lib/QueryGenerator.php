<?php
/**
 *
 */
class lib_QueryGenerator
{

  // Check connection

  public $dbconfig;
  public $table;
  public $column;
  public $where;
  public $whereArray;
  public $order;
  public $value;
  public $query;
  public $insert;
  public $update;
  public $delete;


  function Select()
  {
    global $dbconfig;
    // Create connection
    $conn = new mysqli($dbconfig['db_hostname'], $dbconfig['db_username'], $dbconfig['db_password'], $dbconfig['db_name']);
      $this::CheckDB($conn);
      if(is_array($this->$column))
      {
        $ColList=implode(',', $this->$column);
      } else {
        $ColList='*';
      }
//      $stmt = $conn->prepare("SELECT * FROM user");
      if(is_array($whereArray))
      {
          $stmt->bind_param($whereArray);
      }



      $stmt = $conn->prepare('SELECT '.$ColList.' FROM '.$this->$table.' WHERE name = ?');
      $valueList=array('s','doss');
      $stmt->bind_param($valueList); // 's' specifies the variable type => 'string'

$stmt->execute();

$result = $stmt->get_result();
$userlist;
while ($row = $result->fetch_assoc()) {
    $userlist[]=$row;
}
return $userlist;
  }

  function CheckDB($db)
  {
    if ($db->connect_error) {
      die("Connection failed: " . $db->connect_error);
    }
  }

/*
  $stmt = $conn->prepare("INSERT INTO MyGuests (firstname, lastname, email) VALUES (?, ?, ?)");
  $stmt->bind_param($value);
  $stmt->close();
  */
//$conn->close();
}

 ?>
