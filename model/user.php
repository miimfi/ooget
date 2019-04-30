<?php
class model_user
{
  function UserAthendication()
  {

    global $request;
    $DB=new lib_QueryGenerator();
    $DB->$table='user';
    $DB->$column;
    $DB->$where;
    $DB->$value;

  //  $DB=lib_DB::getData($Query);
    lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$DB->Select()));

  }
}
 ?>
