<?php
function recaptcha($ClientKey)
{
global $SecretKey, $Recaptcha;
/*
    if(isset($_POST['g-recaptcha-response']))
        $captcha=$_POST['g-recaptcha-response'];
*/

    if($Recaptcha=='ON')
    {
      return true;
    }

    if(!$ClientKey){
        $result = 'No client key found';
    }
    if(!$SecretKey){
        $result = 'No secret key found';
    }
    if($result)
    {
      echo $result;
      exit;
    }

    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$SecretKey."&response=".$ClientKey."&remoteip=".$_SERVER['REMOTE_ADDR']), true);
    //print_r($response);
    if($response['success'] == false)
    {
      $result = false;
    }
    else
    {
      $result = true;
    }
return $result;
}
