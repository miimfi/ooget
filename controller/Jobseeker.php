<?php
include("lib/recaptcha.php");
include("model/Jobseeker.php");
use \Firebase\JWT\JWT;
class controller_Jobseeker
{

  function IdVerifiedUpdate()
  {
    isAdmin();
    global $request;
    if($request['jobseekerid'])
    {
      $result=model_Jobseeker::IdVerifiedUpdate($request['jobseekerid'],$request['status']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>500,'success'=>true,'message'=>'updated'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'success'=>false,'message'=>'Update error'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'success'=>false,'message'=>'jobseeker id not found'));
    }
  }

  function ImageUpload()
  {
    global $CurrentUser,$request;
    if($CurrentUser->access=='Jobseeker')
    {
      $request['jobseekerid']=$CurrentUser->id;
    }
    if($request['jobseekerid'])
    {
      if($request['id1'])
      {
        $id_car="_ID1";
      }else if($request['id2']) {
        $id_car="_ID2";
      }
      else {
        $id_car="_PROFILE";
      }
      $target_dir = "media/profile/jobseeker/";
      $realfilename=explode('.',basename($_FILES["fileToUpload"]["name"]));
      $target_file = $target_dir.$request['jobseekerid'].$id_car.'.'.end($realfilename);
      $DB_imgpath=model_Jobseeker::CheckImageStatus($request['jobseekerid']);
      if(($id_car=='_ID1' || $id_car=='_ID2') && $DB_imgpath['id_verified']==1)
      {
          lib_ApiResult::JsonEncode(array('status'=>500,'success'=>false,'message'=>'ID card update locked'));
      }

      $uploadOk = true;
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
      // Check if image file is a actual image or fake image
      $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
      if($check !== false) {

          move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_file);
          $result=model_Jobseeker::Imagepathupdate($CurrentUser->id,$target_file,$id_car);

          lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'upload','imgpath'=>$target_file));
      } else {
          lib_ApiResult::JsonEncode(array('status'=>500,'success'=>false,'message'=>'failure to upload'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'success'=>false,'message'=>'jobseeker id not found'));
    }

  }

  function ImageDelete()
  {
    global $request,$CurrentUser;
    if($request['id1'])
    {
      $id_car="_ID1";
    }else if($request['id2']) {
      $id_car="_ID2";
    }
    else {
      $id_car="_PROFILE";
    }

    if($CurrentUser->access=="Jobseeker")
    {
      $request['jobseekerid']=$CurrentUser->id;
    }
    if($request['jobseekerid'])
    {
      $DB_imgpath=model_Jobseeker::CheckImageStatus($request['jobseekerid']);
      if($request['id1'] && $DB_imgpath['id_imgpath1'])
      {
        isAdmin();
        $fileName=$DB_imgpath['id_imgpath1'];
      }
      elseif($request['id2'] && $DB_imgpath['id_imgpath2'])
      {
        isAdmin();
        $fileName=$DB_imgpath['id_imgpath2'];
      }elseif($CurrentUser->access=="Jobseeker")
      {
        $fileName=$DB_imgpath['imgpath'];
        //$result=model_Jobseeker::Imagepathupdate($request['jobseekerid'],null,$id_car);
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'message'=>'Invalid jobseeker'));
    }

     if(unlink($fileName))
     {
       $delete=true;
     }
      if(!$delete) {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'message'=>'Error'));
      } else {
        $result=model_Jobseeker::Imagepathupdate($request['jobseekerid'],null,$id_car);
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'deleted'));
      }
  }

  function CreateJobseeker()
  {
    global $request,$Recaptcha;
    if( $Recaptcha=='ON')
    {
      $ClientKey=$request['g-recaptcha-response'];
      $Recaptcha=recaptcha($ClientKey);
    }
    if(!$Recaptcha=='success' && $Recaptcha=='ON')
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'recaptcha failed'));
    }
    if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$^", $request['email'])){
              lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Invalid email'));
      }
  else {
    if($request['name'] && $request['email'] && $request['password'] && $request['country'] && ($Recaptcha=='success' ||  $Recaptcha=='OFF'))
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

    if($CurrentUser->access!='admin')
    {
      $request['jobseekerid']=$CurrentUser->id;
      unset($request['status']);
    }

    if($request['jobseekerid'])
    {
      $result=model_Jobseeker::UpdateJobseeker();
      /*if($result && $request['account_no'])
      {
        $result=model_Jobseeker::UpdateJobseekerBankDetail($request['jobseekerid'],$request['bank_id'],$request['branch_code'],$request['account_no']);
      }*/
      if($result>0)
      {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Update error'));
      }

    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Jobseeker ID is empty'));
    }
  }

  function UpdateJobseekerStatus()
  {
    global $request;
    isAdmin();
    if($request['jobseekerid'])
    {
      $result=model_Jobseeker::UpdateJobseekerStatus($request['jobseekerid'],$request['status']);
      if($result>0)
      {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'status changed'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Update error'));
      }

    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Jobseeker ID is empty'));
    }
  }


  function DeleteJobseeker()
  {
    global $request;
    isAdmin();
    if($request['jobseekerid']>0)
    {
      $result=model_Jobseeker::DeleteJobseeker($request['jobseekerid']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'deleted'));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'Invalid jobseeker ID / server error'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'Invalid jobseeker ID'));
    }

  }

  function GetJobseeker()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access=='Jobseeker')
    {
      $request['jobseekerid']=$CurrentUser->id;
    }

    $result=model_Jobseeker::GetJobseeker($request['jobseekerid'],$request['pending']);
    if($result)
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'No data'));
    }

  }

  function FindJobseeker()
  {
    global $request;
    isAdmin();
    $result=model_Jobseeker::FindJobseeker($request['finddata']);
    if($result)
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>409,'result'=>'No data'));
    }
  }

  function CheckUnique()
  {
    global $request;
    if($request['email'])
    {
      if(!preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$^", $request['email'])){
                lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Invalid email'));
        }
    }

    if($request['email'] || $request['mobile'] || $request['nric'])
    {
      $result=model_Jobseeker::CheckUnique($request);
      if(!is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>true,'message'=>'not found'));
      } else {
        lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'message'=>'found'));
      }
    }else {
      lib_ApiResult::JsonEncode(array('status'=>200,'success'=>false,'message'=>'invalid input'));
    }


  }

  /*function GetAppliedList()
  {
    include_once('model/Job.php');
    global $request,$CurrentUser;
    $result=model_Job::GetAppliedList($CurrentUser->id,$request['jobid']);
    if(is_array($result))
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Job'));
    }
  }*/
/*
  function AppliedJob()
  {
    include_once('model/Job.php');
    global $request, $CurrentUser;
    if($CurrentUser->access=='Jobseeker' && $request['jobid'])
    {
      $result=model_Job::AppliedJob($CurrentUser->id,$request);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Job id not found / job closed'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>401,'result'=>'Invalid jobseeker / job id'));
    }
  }


  function GetJobList()
  {
    include_once('model/Job.php');
      global $request;
      $result=model_Job::GetJobList($request['employerid']);
      if(is_array($result))
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'no Job'));
      }
  }

  function GetJobDetails()
  {
    include_once('model/Job.php');
    global $request;
    if($request['jobid']>0)
    {
      $result=model_Job::GetJobDetails($request['jobid']);
      if($result)
      {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Job id not found'));
      }
    }else {
      lib_ApiResult::JsonEncode(array('status'=>500,'result'=>'Invalid Input'));
    }

  }*/

}
