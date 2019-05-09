<?php
class lib_ApiResult
{
  function JsonEncode($data)
  {

    if(is_array($data) && $data['status'])
    {
      // required headers
      // https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
      header_remove();
      global $ServerURL;
      header("Access-Control-Allow-Origin:".$ServerURL);
      header("Content-Type: application/json; charset=UTF-8");
      header("Access-Control-Allow-Methods: POST");
      header("Access-Control-Max-Age: 3600");
      header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

      if($data['status']==400)
      {
        header("HTTP/1.1 400 BAD REQUEST");
        ServerErrorLog("400 BAD REQUEST");
      }else if($data['status']==401)
      {
        header("HTTP/1.1 401 Unauthorized");
      }
      else if($data['status']==200)
      {
        header("HTTP/1.1 200 OK");
      }
      else {
        header("HTTP/1.1 400 BAD REQUES");
        ServerErrorLog("400 BAD REQUEST");
      }

      $data['success']=($data['status']==200?true:false);
      $result=json_encode($data);
      echo $result;
    }
    elseif($data=='error')
    {
      header("Access-Control-Allow-Origin:".$ServerURL);
      header("Content-Type: application/json; charset=UTF-8");
      header("Access-Control-Allow-Methods: POST");
      header("Access-Control-Max-Age: 3600");
      header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
      echo "500 BAD REQUEST";
      ServerErrorLog("500 BAD REQUEST");
      $result=json_encode(array('status'=>500,'success'=>fale,'result'=>'Server error'));
      echo $result;
    }
    else {
      header("HTTP/1.1 400 BAD REQUEST");
      echo "400 BAD REQUEST";
      ServerErrorLog("400 BAD REQUEST");
    }
    ServerResponseLog($result);
    exit;
  }
  function html($data)
  {
    echo "<pre>";
    print_r($data);
    exit;
  }
}
 ?>
