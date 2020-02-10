<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

class Queue extends \Eloquent {
    protected $table = 'mail_queues';
}
