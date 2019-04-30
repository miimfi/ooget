<?php
global $db;
class lib_DB
{
    public function getData($Query)
    {
        $DBC=lib_DB::dbconnect();
      if($Query['table']!='')
      {
        if(is_array($Query['column']))
        {
            $ColList=implode(',', $Query['column']);
        } else {
           $ColList='*';
        }

          $sql="SELECT $ColList From ".$Query['table'];
          $result_Array= $DBC->query($sql);


       if ($result_Array->num_rows > 0)
       {
           while($row = $result_Array->fetch_assoc())
           {
               $result[]=$row;
           }
       } else {
       $result="No data found";
       }
      }
    return $result;
    }

	public function query($sql)
	{

		$DBC=lib_DB::dbconnect();
		$result =$DBC->query($sql);
		return $result;
	}
    public function dbconnect()
    {
	      global $dbconfig;
        $DBC = new mysqli($dbconfig['db_server'], $dbconfig['db_username'], $dbconfig['db_password'], $dbconfig['db_name']);
        if ($DBC->connect_error) {
            die("Connection failed: " . $conn->connect_error);
            }
        else {
                return $DBC;
            }
    }

}

$db=  new lib_DB();
