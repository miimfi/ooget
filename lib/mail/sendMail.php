<?php
include_once('lib/mail/Exception.php');
include_once('lib/mail/PHPMailer.php');
include_once('lib/mail/SMTP.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class SMTPMailer
{
  public $from_ID;
  public $reply_ID;
  public $to_ID;
  public $cc_ID;
  public $bcc_ID;
  public $subject;
  public $body;
  public $plain_text;
  public $attachment;

  public function SendMail()
  {
    global $MailConf_from;
    $this->$from_ID;
    $from_ID=($this->from_ID?$this->from_ID:$MailConf_from);
    $reply_ID=($this->reply_ID?$this->reply_ID:$MailConf_from);
    $to_ID=$this->to_ID;
    $cc_ID=$this->cc_ID;
    $bcc_ID=$this->bcc_ID;
    $subject=$this->subject;
    $body=$this->body;
    $plain_text=$this->plain_text;
    $attachment=$this->attachment;

        global $MailConf_Host,$MailConf_SMTPAuth,$MailConf_Username,$MailConf_Password,$MailConf_SMTPSecure,$MailConf_Port,$MailConf_SMTPDebug;
        $mail = new PHPMailer();
        //===================
        try {
            //Server settings

            $mail->isSMTP();                                            // Set mailer to use SMTP
            $mail->Host       = $MailConf_Host;  // Specify main and backup SMTP servers
            $mail->SMTPAuth   = $MailConf_SMTPAuth;                                   // Enable SMTP authentication
            $mail->Username   = $MailConf_Username;                     // SMTP username
            $mail->Password   = $MailConf_Password;                               // SMTP password
            $mail->SMTPSecure = $MailConf_SMTPSecure;                                  // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = $MailConf_Port;                        // TCP port to connect to
            $mail->SMTPDebug  = $MailConf_SMTPDebug;                                // Enable verbose debug output

            //Recipients
            $mail->setFrom($from_ID['id'],$from_ID['name']);
            $mail->addReplyTo($reply_ID['id'],$reply_ID['name']);

            //$mail->addAddress('schidambaradoss@gmail.com', 'doss');     // Add a recipient
            if($to_ID['id'])
            {
              $mail->addAddress($to_ID['id'], $to_ID['name']);
            }
            elseif(is_array($to_ID[0]))
            {
              foreach ($to_ID as $key => $value) {
                $mail->addAddress($value['id'], $value['name']);
              }
            }
            else {
              return array('status' => false,'result'=>"invalid to");
            }


          //  $mail->addCC('cc@example.com');
          if($cc_ID['id'])
          {
            $mail->addCC($cc_ID['id'], $cc_ID['name']);
          }
          elseif(is_array($cc_ID[0]))
          {
            foreach ($cc_ID as $key => $value) {
              $mail->addCC($value['id'], $value['name']);
            }
          }

          //  $mail->addBCC('bcc@example.com');
          if($bcc_ID['id'])
          {
            $mail->addBCC($bcc_ID['id'], $bcc_ID['name']);
          }
          elseif(is_array($bcc_ID[0]))
          {
            foreach ($bcc_ID as $key => $value) {
              $mail->addBCC($value['id'], $value['name']);
            }
          }

            // Attachments
          //  $mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
          foreach ($attachment as $key => $value) {
            $mail->addAttachment($value);
          }

            // Content
            $mail->isHTML(true);
            if(!$subject)
            {
              return array('status' => false,'result'=>"Subject is empty");
            }
            if(!$body && !$plain_text)
            {
              return array('status' => false,'result'=>"Body is empty");
            }                                 // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = $plain_text;

            $mail->send();
            return array('status' => true,'result'=>'success' );
        } catch (Exception $e) {
          return array('status' => false,'result'=>"Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
  }

}



 ?>
