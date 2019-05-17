<?php
include_once('model/Employer.php');
use \Firebase\JWT\JWT;
class controller_Employer
{

  function ImageUpload()
  {
    global $request;
    if($request['employerid'])
    {
      $target_dir = "media/profile/employer/";
      $realfilename=explode('.',basename($_FILES["fileToUpload"]["name"]));
      $target_file = $target_dir.$request['employerid'].'.'.end($realfilename);
      $uploadOk = true;
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
      // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check !== false) {
          move_uploaded_file($_FILES["fileToUpload"]["tmp_name"],$target_file);
          lib_ApiResult::JsonEncode(array('status'=>200,'message'=>'upload'));
        } else {
          lib_ApiResult::JsonEncode(array('status'=>200,'message'=>'failure to upload'));
        }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'message'=>'employer id not found'));
    }

  }

  function ImageDelete()
  {
    global $request;
    if($request['employerid'])
    {
    $target_dir = "media/profile/employer/";
    $target_file = $target_dir.$request['employerid'].'.*';
    foreach (glob($target_file) as $fileName) {
     if(unlink($fileName))
     {
       $delete=true;
     }
    }
      if(!$delete) {
        lib_ApiResult::JsonEncode(array('status'=>200,'message'=>'error'));
      } else {
        lib_ApiResult::JsonEncode(array('status'=>200,'message'=>'deleted'));
      }
    }
    else {
        lib_ApiResult::JsonEncode(array('status'=>200,'message'=>'employer id not found'));
      }
  }

  function CheckCompanyCodeExist()
  {
    global $request;
    if($request['companycode'])
    {
      $CheckCompanyCodeExist=model_Employer::CheckCompanyCodeExist($request['companycode']);
      lib_ApiResult::JsonEncode(array('status'=>200,'success'=>($CheckCompanyCodeExist?false:true),'message'=>($CheckCompanyCodeExist?'companycodeexists':'companycodenotexists')));
    }else {
    }
    lib_ApiResult::JsonEncode(array('status'=>400,'message'=>'InvalidInput'));
  }

  function CheckCompanyUenExist()
  {
    global $request;
    if($request['uen'])
    {
      $CheckCompanyExist=model_Employer::CheckCompanyExist($request['uen']);
      lib_ApiResult::JsonEncode(array('status'=>200,'success'=>($CheckCompanyExist?false:true),'message'=>($CheckCompanyExist?'companyexists':'companynotexists')));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>400,'message'=>'InvalidInput'));
    }
  }

  function CreateEmployer()
  {
    global $request;
    if($request['companyname'] && $request['profile'] && $request['uen'] && $request['industry'] && $request['country'] && $request['username'] && $request['useremail'] && $request['password'])
    {
      $CheckCompanyExist=model_Employer::CheckCompanyExist($request['uen']);
      //$CheckCompanyCodeExist=model_Employer::CheckCompanyExist($request['companycode'],$request['uen']);
      if(!$CheckCompanyExist)
      {
        $result=model_Employer::CreateEmployer();
        if($result>0)
        {
          lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Employer Created'));
        }
        else {
          lib_ApiResult::JsonEncode('error');
        }
      }
      else {
        lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Company code / UEN already exist'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Please fill all mandatory fields'));
    }

  }
  function UpdateEmployer()
  {
    global $request;
    if($request['employerid'])
    {
      $result=model_Employer::UpdateEmployer();
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Employer ID is empty'));
    }
  }

  function FindEmployer()
  {
    global $request;
    isAdmin();
    $result=model_Employer::FindEmployer($request['finddata']);
    if($result)
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>409,'result'=>'No data'));
    }
  }

  function DeleteEmployer()
  {
    global $request;
    $isfound= model_Employer::GetEmployer($request['employerid']);
    if($request['employerid'] && $isfound)
    {
      $result=model_Employer::DeleteEmployer($request['employerid']);
      if($result)
      {lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'deleted'));}
      else {
        lib_ApiResult::JsonEncode(array('status'=>400,'result'=>'Invalid id'));
      }
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'Employer ID not found'));
    }
  }

  function GetEmployer()
  {
    global $request,$CurrentUser;
    if($CurrentUser->access!='admin')
    {
      $request['employerid']=($CurrentUser->companyid?$CurrentUser->companyid:'empty');
    }
    if($request['employerid'])
    {
      $result=model_Employer::GetEmployer($request['employerid']);
    }
    else {
      $result=model_Employer::GetEmployer();
    }

    if($result)
    {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
    }
    else {
      lib_ApiResult::JsonEncode(array('status'=>200,'result'=>'No data'));
    }


  }
}
