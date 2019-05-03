<?php
include_once('model/User.php');
use \Firebase\JWT\JWT;
class controller_Users
{
/*
  function __construct(argument)
  {
    // code...
  }*/
  function Authentication()
  {
    global $JWTKey, $CurrentUser;
    if($_SERVER['HTTP_TOKEN'])
    {
      try {
      $decoded = JWT::decode($_SERVER['HTTP_TOKEN'], $JWTKey, array('HS256'));
      if($decoded->userdetails->email)
        {
          $CurrentUser=$decoded->userdetails;
          return true;
        }
        else {
          return false;
        }
      }
      catch(Exception $e) {
          return false;
        }

    }
  //  return true;
  }
  function Login()
  {
    $result=model_User::UserAuthentication();
    global $JWTExpireTime,$JWTKey;

    if($result['email'])
    {
      $issuedAt = time();
      $expirationTime = $issuedAt + $JWTExpireTime;  // jwt valid for 60 seconds from the issued time
      $payload = array(
        'userdetails' => $result,
        'issuedAt' => $issuedAt,
        'exp' => $expirationTime,
      );
      $key = $JWTKey;
      $alg = 'HS256';
      $jwt = JWT::encode($payload, $key, $alg);
      $result['token']=$jwt;
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'login failed'));
    }
  }

  function CreateUser()
  {
    global $request;
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $request['email'])){
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Invalid email'));
      }
      else {
        if($request['name'] && $request['password'] && (($request['role'] && $request['type']!='admin')|| $request['type']=='admin') && $request['email'])
        {
          $CheckEmail=model_User::CheckEmail($request['email']);
          if(!$CheckEmail)
          {
            $result=model_User::CreateUser();
            if($result>0)
            {
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'User Created'));
            }
            else {
              lib_ApiResult::JsonEncode('error');
            }
          }
          else {
            lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Email id already exist'));
          }
        }else {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Please fill all mandatory fields'));
        }
    }
  }

  function CreateJobseeker()
  {
    if($request['firstname'] && $request['password'] && $request['role'] && $request['email'])
    {
      model_User::CreateAdmin();
    }else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Please fill all mandatory fields'));
    }
  }

}
