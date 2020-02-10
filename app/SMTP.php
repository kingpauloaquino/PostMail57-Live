<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class SMTP extends \Eloquent {
    protected $table = 'mail_smtp';
}
