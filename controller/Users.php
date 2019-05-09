<?php
include_once('model/User.php');
use \Firebase\JWT\JWT;
class controller_Users
{
  function CheckEmail()
  {
    global $request;
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $request['emailid'])){
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Invalid email'));
      }
      else
      {
          $result=model_User::CheckEmail($request['emailid']);
          if(!is_array($result))
          {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'email id not found'));
          } else {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>fales,'message'=>'email id found'));
          }
      }
  }

  function ImageUpload()
  {
    global $CurrentUser;
    $target_dir = "media/profile/user/";
    $realfilename=explode('.',basename($_FILES["fileToUpload"]["name"]));
    $target_file = $target_dir.$CurrentUser->id.'.'.end($realfilename);
    $uploadOk = true;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image

      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
      if($check !== false) {
        move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_file);
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'upload'));
      } else {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>fales,'message'=>'failure to upload'));
      }

  }

  function ImageDelete()
  {
    global $CurrentUser;
    $target_dir = "media/profile/user/";
    $target_file = $target_dir.$CurrentUser->id.'.*';
    foreach (glob($target_file) as $fileName) {
     if(unlink($fileName))
     {
       $delete=true;
     }
    }
      if(!$delete) {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>fales,'message'=>'error'));
      } else {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'deleted'));
      }
  }

  function Login()
  {
    $result=model_User::Login();
    global $JWTExpireTime,$JWTKey;

    if($result['email'])
    {
      $issuedAt = time();
      $expirationTime = $issuedAt + $JWTExpireTime;
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

  function logout()
  {

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

}
