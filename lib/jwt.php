<?php
include('lib/jwt/BeforeValidException.php');
include('lib/jwt/ExpiredException.php');
include('lib/jwt/SignatureInvalidException.php');
include('lib/jwt/JWT.php');
use \Firebase\JWT\JWT;
/**
 *
 */

class lib_jwt
{

  function Authentication()
  {
    global $JWTKey, $CurrentUser,$request;
    if($_SERVER['HTTP_TOKEN'])
    {
      $token=$_SERVER['HTTP_TOKEN'];
    }
    else{
      $token=$request['token'];
    }

    if($token)
    {
      try {
      $decoded = JWT::decode($token, $JWTKey, array('HS256'));
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
  }
}

 ?>
