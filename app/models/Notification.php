<?php

use Phalcon\Mvc\MongoCollection;

class Notification extends MongoCollection
{
    public $app_id;
    public $sid;
    public $mobile;
    public $request_id;
    public $ref_id;
    public $type;
    public $otp;
    public $text;
    public $expiration;
    public $client_ip;
    public $status;
    public $callback;
    public $created_at;
    public $updated_at;
    public $deleted_at;

    public function initialize()
    {
        $this->setSource('notifications');
    }
}