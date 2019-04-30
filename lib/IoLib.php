<?php
function frame_request(){
  global $request;
  foreach($_REQUEST as $key => $val){
         if($val !=''){
                      $request[$key] = stripslashes($val);
            }else
                       $request[$key] = $val;
        }
        RequestLog();
}
function RequestLog($path=0)
{
    global $request, $ServerRequestLog;
  // Program to display URL of current page.

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https";
else
    $link = "http";
// Here append the common URL characters.
$link .= "://";
// Append the host(domain name, ip) to the URL.
$link .= $_SERVER['HTTP_HOST'];
// Append the requested resource location to the URL
$link .= $_SERVER['REQUEST_URI'];
if(is_array($request))
$link .="?".http_build_query($request, null, '&');


  if($ServerRequestLog==1 )
  {
  $logfile = 'log/request_'.date("d-m-Y").'.log';
  $logtxt = date("h:i:sa")."\t".$link."\n";
  file_put_contents($logfile, $logtxt, FILE_APPEND);
  }
}
function ServerResponseLog($data)
{
  global $ServerResponseLog;
  if($ServerResponseLog==1)
  {
    $logfile = 'log/response_'.date("d-m-Y").'.log';
    $logtxt = date("h:i:sa")."\t".$data."\n";
    file_put_contents($logfile, $logtxt, FILE_APPEND);
  }
}
function ServerErrorLog($data)
{
  global $request, $ServerErrorLog;
  // Program to display URL of current page.

if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https";
else
    $link = "http";
// Here append the common URL characters.
$link .= "://";
// Append the host(domain name, ip) to the URL.
$link .= $_SERVER['HTTP_HOST'];
// Append the requested resource location to the URL
$link .= $_SERVER['REQUEST_URI'];
if(is_array($request))
$link .="?".http_build_query($request, null, '&');
  if($ServerErrorLog==1)
  {
    $logfile = 'log/error_'.date("d-m-Y").'.log';
    $logtxt = date("h:i:sa")."\t".$data."\t".$link."\n";
    file_put_contents($logfile, $logtxt, FILE_APPEND);
  }
}

frame_request();
 ?>
