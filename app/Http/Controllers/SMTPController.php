<?php

namespace App\Http\Controllers;

use App\Http\Requests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

use \Swift_Mailer;
use \Swift_SmtpTransport as SmtpTransport;

use App\SMTP;
use App\Queue;
use DB;

class SMTPController extends Controller
{
    public function eloadster()
    {
        return view('mail.eloadster');
    }

    public function fetch() {
      $queue = DB::select("SELECT Id AS qid, mail_to AS recipient
              FROM mail_queues
              WHERE mail_status = 1
              ORDER BY created_at ASC;");

      if( COUNT($queue) > 0) {
        return array(
          "code" => 200,
          "message" => "success",
          "count" => COUNT($queue),
          "data" => $queue
        );
      }
      return array(
        "code" => 404,
        "message" => "no records",
        "count" => 0,
        "data" => []
      );
  }

    public function send($qid) {
      $time_start = microtime(true);

      $mail = $this->getSpecificEmail((int)$qid);
      if($mail == null) {
          return array(
              "code" => 400,
              "message" => "No email to be sent",
              "email" => null
          );
      }

      $qid = $mail[0]->Id;
      $is_prod = $mail[0]->mail_prod > 0 ? true : false;
      $user_id = IsSet($mail[0]->user_id) ? $mail[0]->user_id : 0;
      $mail_to = IsSet($mail[0]->mail_to) ? $mail[0]->mail_to : null;
      $mail_template = IsSet($mail[0]->mail_template_name) ? $mail[0]->mail_template_name : null;

      if($mail_to == null || $user_id == 0 || $mail_template == null) {
          $this->updateEmailQueue($qid, 401);
          return array(
              "code" => 401,
              "message" => "Something wrong with Email or User or Template. ID: {$qid}",
              "email" => null
          );
      }

      $this->getUserSMTPConfig($user_id);

      $temp = DB::select("SELECT * FROM mail_templates WHERE user_id = {$user_id} AND mail_name = '{$mail_template}' AND mail_status = 1;");
      if($temp == null) {
          $this->updateEmailQueue($qid, 402);
          return array(
              "code" => 402,
              "message" => "Email templates did not found. ID: {$qid}",
              "email" => null
          );
      }

      $sqlString = "
        SELECT
        	u.*,
        	d.user_company_name,
        	d.user_company_domain,
        	d.user_company_email
        FROM
        	users AS u
        JOIN
        	user_details AS d
        ON
        	u.id = d.user_id
        WHERE u.id = {$user_id} and u.status = 1;";

      $user_info = DB::select($sqlString);

      if($user_info == null) {
          $this->updateEmailQueue($qid, 403);
          return array(
              "code" => 403,
              "message" => "Account did not found. ID: {$qid}",
              "email" => null
          );
      }

      $body = $temp[0]->mail_body;
      $body = str_replace("[NAME]", $mail[0]->mail_name, $body);
      $body = str_replace("[BODY_MESSAGE]", $mail[0]->mail_message, $body);
      $body = str_replace("[COMPANY_NAME]", $user_info[0]->user_company_name, $body);
      $body = str_replace("[COMPANY_DOMAIN]", $user_info[0]->user_company_domain, $body);
      $body = str_replace("[COMPANY_EMAIL]", $user_info[0]->user_company_email, $body);
      $body = str_replace("[SUBJECT]", $mail[0]->mail_subject, $body);
      $body = str_replace("[CR_YEAR]", date("Y"), $body);

      $carbon_copy = null;
      if($mail[0]->mail_cc != null) {
        $carbon_copy = explode(", ", $mail[0]->mail_cc);
      }

      $bcc = ['pollystore.a@gmail.com'];

      $data = [
          "body" => $body,
          "name" => $mail[0]->mail_name,
          "to" => $mail[0]->mail_to,
          "bcc" => $bcc,
          "cc" => $carbon_copy,
          "subject" => $mail[0]->mail_subject
      ];

      $type = "mail.layout";
      $email_execution_time = null;

      try{

          $email_time_start = microtime(true);

          if($carbon_copy != null) {
            Mail::send($type, $data, function($message) use ($data)
            {
                $message
                    ->to($data["to"], $data["to"])
                    ->cc($data["cc"])
                    ->bcc($data["bcc"])
                    ->subject($data["subject"]);
            });
          }
          else {
            Mail::send($type, $data, function($message) use ($data)
            {
                $message
                    ->to($data["to"], $data["to"])
                    ->bcc($data["bcc"])
                    ->subject($data["subject"]);
            });
          }

          $email_time_end = microtime(true);
          $email_execution_time = ($email_time_end - $email_time_start);

      }
      catch(\Exception $e){
          $this->updateEmailQueue($qid, 500);
          return array(
              "Status" => 500,
              "Message" => "{$e} | ID: {$qid}",
              "Email" => $mail[0]->mail_to
          );
      }

      if(count(Mail::failures()) > 0){
          $this->updateEmailQueue($qid, 501);
          return array(
              "code" => 500,
              "message" => Mail::failures(),
              "email" => $mail[0]->mail_to
          );
      }

      $this->updateEmailQueue($qid, 200);

      $time_end = microtime(true);

      $process_execution_time = ($time_end - $time_start);

      return array(
          "code" => 200,
          "message" => "Email Sent. ID: {$qid}",
          "email_time" => $email_execution_time,
          "process_time" => $process_execution_time,
          "email" => $mail[0]->mail_to
      );
    }

    public function getSpecificEmail(int $qid) {
      $queue = DB::select("SELECT *
              FROM mail_queues
              WHERE Id = {$qid};");
      return $queue;
    }

    public function updateEmailQueue(int $qid, int $status) {
        $queue = Queue::where("Id", $qid)
            ->update( array("mail_status" => $status) );
        return $queue;
    }

    public function getUserSMTPConfig(int $uid) {
      $config = SMTP::where("Id", $uid)->first();
      $configs = array(
          'driver' => 'smtp',
          'host' => $config->host,
          'port' =>  $config->port,
          'from' => array('address' => $config->username, 'name' =>  $config->name),
          'encryption' =>  $config->encryption,
          'username' =>  $config->username,
          'password' =>  $config->password,
          'sendmail' => '/usr/sbin/sendmail -bs',
          'pretend' => false,
      );
      return $this->overrideMailerConfig($configs);
    }

    public function overrideMailerConfig($configs){
      Config::set('mail.driver', $configs['driver']);
      Config::set('mail.host', $configs['host']);
      Config::set('mail.port', $configs['port']);
      Config::set('mail.from', $configs['from']);
      Config::set('mail.encryption', $configs['encryption']);
      Config::set('mail.username', $configs['username']);
      Config::set('mail.password', $configs['password']);
      Config::set('mail.sendmail', $configs['sendmail']);

      extract(Config::get('mail'));

      // create new mailer with new settings
      $transport = (new \Swift_SmtpTransport($configs['host'], $configs['port']))
                           ->setUsername($configs['username'])
                           ->setPassword($configs['password'])
                           ->setEncryption($configs['encryption']);

      \Mail::setSwiftMailer(new \Swift_Mailer($transport));

      $app = App::getInstance();
      // $app['swift.transport'] = $app->share(function ($app) {
      //     return new TransportManager($app);
      // });
      //
      // $mailer = new \Swift_Mailer($app['swift.transport']->driver());
      // Mail::setSwiftMailer($mailer);
      return $app['config']['mail'];
    }
}
