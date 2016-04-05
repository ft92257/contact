<?php

use \Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $_tableName = 'tb_friend';

    const TYPE_FRIEND = 1;//好友
    const TYPE_ATTENTION = 2;//关注

    private static $friends = null;

    protected function _format(&$row)
    {
        $row['friend'] = \User::m()->getInfo($row['friend_uid']);
    }

    /**
     * 判断是否我的好友
     * @param $friend_uid
     * @return bool
     */
    public function isFriend($friend_uid)
    {
        return (bool) $this->getFriends($friend_uid);
    }

    /**
     * 获取我的好友信息
     * @param $friend_uid
     * @param string $field 默认获取整个数组
     */
    public function getFriends($friend_uid, $field = '') {
        if (self::$friends === null) {
            $friends = $this->getAll([
                'fields' => 'friend_uid',
                'where'  => [
                    'type'   => self::TYPE_FRIEND,
                    'status' => 2,
                    'uid'    => $this->uid,
                ],
            ]);
            self::$friends = Func::setArrayKey($friends, 'friend_uid');
        }

        if (isset(self::$friends[$friend_uid])) {
            return $field ? self::$friends[$friend_uid][$field] : self::$friends[$friend_uid];
        } else {
            return '';
        }
    }
}