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
/*
  200 	OK
  201 	Created
  304 	Not Modified
  400 	Bad Request
  401 	Unauthorized
  403 	Forbidden
  404 	Not Found
  422 	Unprocessable Entity
  500 	Internal Server Error
*/
      switch ($data['status']) {
        case 200:
          header("HTTP/1.1 200 OK");
          break;

        case 201:
          header("HTTP/1.1 201 Created");
          break;

        case 304:
          header("HTTP/1.1 304 	Not Modified");
          break;

        case 400:
          header("HTTP/1.1 400 Bad Request");
          ServerErrorLog("400 Bad Request");
          break;

        case 401:
          header("HTTP/1.1 401 Unauthorized");
          break;

        case 403:
          header("HTTP/1.1 403 Forbidden");
          break;

        case 404:
          header("HTTP/1.1 404 Not Found");
          break;

        case 422:
          header("HTTP/1.1 422 Unprocessable Entity");
          break;

        case 500:
          header("HTTP/1.1 500 Internal Server Error");
          ServerErrorLog("500 Internal Server Error");
          break;

        default:
            header("HTTP/1.1 400 Bad Request");
            ServerErrorLog("400 Bad Request");
          break;
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
