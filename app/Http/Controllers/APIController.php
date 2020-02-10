<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Queue;
class APIController extends Controller
{
  public function send(Request $request) {

    $mail_prod = 0;
    if(IsSet($request->prod)) {
      $mail_prod = 1;
    }

    $uid = (int)$request->id;
    $body = $request->message;

    $queue = new Queue();
    $queue->user_id = $uid;
    $queue->mail_name = $request->name;
    $queue->mail_to = $request->email;
    $queue->mail_cc = $request->ccopy;
    $queue->mail_subject = $request->subject;
    $queue->mail_message = $body;
    $queue->mail_template_name = $request->template;
    $queue->mail_prod = $mail_prod;
    $queue->mail_status = 1;
    $result = $queue->save();

    if($result) {
      return array(
        "code" => 200,
        "message" => "Success"
      );
    }

    return array(
      "code" => 500,
      "message" => "Fail"
    );

  }
}
