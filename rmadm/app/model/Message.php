<?php

use \Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $_tableName = 'tb_message';

    /**
     * 最近聊天的人数据处理
     * @param $chats
     */
    public function formatLastChat($chats) {
        $ret = [];
        foreach ($chats as $chat) {
            $chat['user'] = \User::m()->getInfo($chat['uid']);
            $chat['user']['realname'] = \Friend::m()->getFriends($chat['uid'], 'remark');
            $ret[] = $chat;
        }

        return $ret;
    }

}