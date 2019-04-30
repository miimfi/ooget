<?php
include_once('model/user.php');
//lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$request));
$result=model_user::UserAthendication();
lib_ApiResult::JsonEncode(array('status'=>200,'result'=>$result));
