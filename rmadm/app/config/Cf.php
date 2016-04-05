<?php

class Cf
{
    public static function getRootPath()
    {
        return ROOT;
    }

    public static function C($key)
    {
        $config = include(dirname(self::getRootPath()) . '/config.php');
        if (!isset($config[$key])) {
            throw new Exception($key . ' 配置不存在！');
        }
        return $config[$key];
    }

    public static function getPathUrl($action, $controller) {
        if (!$action) {
            $action = ACTION;
        }
        if (!$controller) {
            $controller = CONTROLLER;
        }

        return '/'.$controller.'/' . $action;
    }


    public static function getDbConfig($dbkey)
    {
        if ($dbkey == '') {
            $dbkey = 'db';
        }
        if ($dbkey == 'db') {
            $cfg = parse_url(self::C('DB_CONFIG'));
        } elseif ($dbkey == 'molbase') {
            $cfg = parse_url(self::C('DB_MOLBASE'));
        } else {
            die('没有该数据库配置' . $dbkey);
        }
        $config = [
            'host'     => $cfg['host'] . (isset($cfg['port']) ? ':' . $cfg['port'] : ''),
            'user'     => $cfg['user'],
            'password' => $cfg['pass'],
            'dbname'   => str_replace('/', '', $cfg['path']),
            'charset'  => 'utf8',
        ];

        return $config;
    }

    public static function getRedisConfig()
    {
        return [
            'host'     => self::C('REDIS_HOST'),
            'port'     => self::C('REDIS_PORT'),
            'password' => self::C('REDIS_PASSWORD'),
            'pre'      => 'renmai_',
        ];
    }

}

?>