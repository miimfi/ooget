<?php
include_once('model/User.php');
use \Firebase\JWT\JWT;
include_once('lib/mail/sendMail.php');
class controller_Users
{
  function CheckEmail()
  {
    global $request;
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $request['email'])){
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Invalid email'));
      }
      else
      {
          $result=model_User::CheckEmail($request['email']);
          if(!is_array($result))
          {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'email id not found'));
          } else {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'message'=>'email id found'));
          }
      }
  }

  function UpdatePassword()
  {
    global $request,$CurrentUser;
    $user_id=$CurrentUser->id;
    if($request['oldpassword'] && $request['password'])
    {
      $result=model_User::UpdatePassword($user_id,$request['oldpassword'],$request['password']);
      if($result)
      {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Password Updated'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid current password'));
      }

    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid Password'));
    }
  }

  function ForgotPassword()
  {
    global $website_Name,$request,$ForgotPasswordReturnURL;

    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $request['email'])){
              lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid email'));
      }

    $key=model_User::ForgotPassword($request['email']);
    //print_r($key);exit;
    if(is_array($key))
    {
      $mail_data=new SMTPMailer;
      //$mail_data->from_ID=;
      //$mail_data->reply_ID=;
      $mail_data->to_ID=array('id' =>$key['email'] ,'name'=>$key['firstname'].' '.$key['lastname']);
      //$mail_data->cc_ID=;
      //$mail_data->bcc_ID=;
      $mail_data->subject=$website_Name."- Password recovery Mail";
      $mail_body_test="<h3>Hi ".$key['firstname'].' '.$key['lastname'].",</h3>";
      $mail_body_test.='<br><br> Your last login time : '.$key['lastlogin'];
      $mail_body_test.='<br><a href="'.$ForgotPasswordReturnURL.'?key='.$key['key'].'&email='.$key['email'].'">Please click to reset your password</a><br>';
      $mail_data->body=$mail_body_test;
      $mail_data->plain_text=" ";
      $result = $mail_data->SendMail();
      if($result['status'])
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Please check on your mail :'.$key['email']));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Server error, Please try later'));
      }

    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid email'));
    }

  }

  function ForgotPasswordUpdate()
  {
    global $request;
    if($request['key'] && $request['mail'] && $request['password'])
    {
      $result=model_User::ForgotPasswordUpdate($request['key'],$request['mail'],$request['password']);
      if($result)
      {
          $request['email']=$request['mail'];
          $request['password']=$request['password'];
          controller_Users::Login();
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid input'));
      }

    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid input'));
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
        $result=model_User::Imagepathupdate($CurrentUser->id,$target_file);
      //$result=true;
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'upload','imgpath'=>$target_file));

      } else {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'message'=>'failure to upload'));
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
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'message'=>'error'));
      } else {
        $result=model_User::Imagepathupdate($CurrentUser->id,$target_file);
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
    require_once('model/Employer.php');
    global $request,$CurrentUser;
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^", $request['email'])){
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Invalid email'));
      }
      else {
        if($CurrentUser->access!='admin')
        {
          $request['companyid']=$CurrentUser->companyid;
          if($request['type']=='admin')
          {
            lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'your not allow to create admin user, because your not OOGET admin'));
          }
        }

        if($request['name'] && $request['password'] && ($request['companyid']>0 || $request['type']=='admin') && $request['email'])
        {
          $CheckEmail=model_User::CheckEmail($request['email']); //check email id
          $CheckEmployer=model_Employer::GetEmployer($request['companyid']);
          if(!is_array($CheckEmployer))
          {
            // check if company is found
            lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'company not found'));
          }
          if(!$CheckEmail)
          {
            $result=model_User::CreateUser($request);
            if($result>0)
            {
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'User Created'));
            }
            else {
              lib_ApiResult::JsonEncode('error');
            }
          }
          else {
            lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>'Email id already exist'));
          }
        }else {
          lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'result'=>'Please fill all mandatory fields'));
        }
    }
  }


  function GetUserList()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access!='admin')
    {
      $request['employerid']=$CurrentUser->companyid;
      if($CurrentUser->access!='employer'  || $request['employerid']<1)
      {
        lib_ApiResult::JsonEncode(array('status'=>403,'result'=>'your not allow to access'));
      }
    }

    if($request['employerid']>0)
    {
      $result =model_User::GetUserList($request['employerid']);
    }
    else {
      $result =model_User::GetUserList();
    }
    if(is_array($result))
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>400,'result'=>$result));
    }


  }

  function GetUser()
  {
    global $CurrentUser, $request;
    if(($CurrentUser->access!='admin' && $CurrentUser->access!='employer') || !$request['userid'])
    {
      $request['userid']=$CurrentUser->id;
    }

    if($request['userid'])
    {
    $result =model_User::GetUser($request['userid']);
    if(is_array($result))
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'no details found'));
    }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid user id'));
    }
  }

  function DeleteUser()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access!='admin' && $CurrentUser->access!='employer' )
    {
      lib_ApiResult::JsonEncode(array('status'=>403,'result'=>'your not allow to delete'));
    }

    if($CurrentUser->id==$request['userid'])
    {
      lib_ApiResult::JsonEncode(array('status'=>403,'result'=>'your not allow to delete your account'));
    }

    if($request['userid']>0)
    {
      if($CurrentUser->access!='admin')
      {
          if($CurrentUser->companyid>0)
          {
            $request['companyid']=$CurrentUser->companyid;
          }
          else {
            lib_ApiResult::JsonEncode(array('status'=>403,'result'=>'your not allow to delete'));
          }
      }
      $result =model_User::DeleteUser($request['userid'],$request['companyid']);
      if($result)
      {lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'deleted'));}
      else {
        lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'Invalid user id'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'Invalid user id'));
    }
  }

}
