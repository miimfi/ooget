<?php
include("lib/recaptcha.php");
include("model/Jobseeker.php");
use \Firebase\JWT\JWT;
class controller_Jobseeker
{

  function ImageUpload()
  {
    global $CurrentUser,$request;
    if($CurrentUser->access=='Jobseeker')
    {
      $request['jobseekerid']=$CurrentUser->id;
    }
    if($request['jobseekerid'])
    {
      $target_dir = "media/profile/jobseeker/";
      $realfilename=explode('.',basename($_FILES["fileToUpload"]["name"]));
      $target_file = $target_dir.$request['jobseekerid'].'.'.end($realfilename);
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
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'success'=>fales,'message'=>'jobseeker id not found'));
    }

  }

  function ImageDelete()
  {
    global $CurrentUser;
    $target_dir = "media/profile/jobseeker/";
    $target_file = $target_dir.$CurrentUser->id.'.*';
    foreach (glob($target_file) as $fileName) {
     if(unlink($fileName))
     {
       $delete=true;
     }
    }
      if(!$delete) {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>fales,'message'=>'Error'));
      } else {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'deleted'));
      }
  }

  function CreateJobseeker()
  {
    global $request;
    $ClientKey=$request['g-recaptcha-response'];
    $Recaptcha=recaptcha($ClientKey);
    if(!$Recaptcha=='success')
    {lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'recaptcha failed'));}
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $request['email'])){
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Invalid email'));
      }
      else {
    if($request['name'] && $request['email'] && $request['pass'] && $request['country'] && $Recaptcha=='success')
    {
      $CheckEmail=model_Jobseeker::CheckEmail($request['email']);
      if(!$CheckEmail)
      {
        $result=model_Jobseeker::CreateJobseeker();
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


    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Please fill all mandatory fields'));
      }
    }
  }

  function Login()
  {
    $result=model_Jobseeker::Login();
    global $JWTExpireTime,$JWTKey;
    if($result['email'])
    {
      $issuedAt = time();
      $expirationTime = $issuedAt + $JWTExpireTime;  // jwt valid for 60 seconds from the issued time
      $result['access']='Jobseeker';
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

  function UpdateJobseeker()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=='Jobseeker')
    {
      $request['jobseekerid']=$CurrentUser->id;
    }

    if($request['jobseekerid'])
    {
      $result=model_Jobseeker::UpdateJobseeker();
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Jobseeker ID is empty'));
    }
  }

  function GetJobseeker()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=='Jobseeker')
    {
      $result=model_Jobseeker::GetJobseeker($CurrentUser->id);
    }
    else if(!$request['jobseekerid'])
    {
      $result=model_Jobseeker::GetJobseeker();
    }
    else {
      $result=model_Jobseeker::GetJobseeker($request['jobseekerid']);
    }

    if($result)
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'No data'));
    }

  }

  function CheckEmail()
  {
    global $request;
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $request['emailid'])){
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Invalid email'));
      }
      else
      {
          $result=model_Jobseeker::CheckEmail($request['emailid']);
          if(!is_array($result))
          {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'email id not found'));
          } else {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>fales,'message'=>'email id found'));
          }
      }
  }

}
