<?php
/**
 * Created by PhpStorm.
 * User: apuc0
 * Date: 22.02.2016
 * Time: 16:27
 */


class Mailer
{

    public $header;
    public $charset = 'utf-8';
    public $type = 'html';
    public $message;
    public $to;
    public $from = false;
    public $cc = false;
    public $subject = 'Письмо';

    function __construct()
    {
        $this->header = "Content-type: text/html; charset=$this->charset\r\n";
    }

    public function send()
    {
        if($this->from){
            $this->header .= "From: $this->from\r\n";
        }
        if($this->cc){
            $this->header .= "Cc: $this->cc\r\n";
        }
        return mail($this->to, $this->subject, $this->message, $this->header);
    }

}