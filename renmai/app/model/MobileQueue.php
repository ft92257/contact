<?php

use \Illuminate\Database\Eloquent\Model;

class MobileQueue extends Model
{
    protected $_tableName = 'ecm_mob_queue';
    protected $_dbConfig = 'molbase';

    public function send($mobile, $subject, $content)
    {
        $data = [
            'mobile'   => $mobile,
            'subject'  => $subject,
            'content'  => $content,
            'add_time' => time(),
            'source'   => 'renmai',
        ];

        return $this->addData($data);
    }
}