<?php

class Cf
{
    private static $_langs = null;

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
            'charset'  => 'utf8mb4',
        ];

        return $config;
    }

    public static function getRedisConfig()
    {
        return [
            'host'     => self::C('REDIS_HOST'),
            'port'     => self::C('REDIS_PORT'),
            'password' => self::C('REDIS_AUTH'),
            'pre'      => 'renmai_',
        ];
    }

    public static function isIos()
    {
        return $_REQUEST['clientType'] == 2;//安卓1，ios 2
    }

    public static function lang($code)
    {
        if (self::$_langs === null) {
            self::$_langs = include(self::getRootPath() . '/app/config/langs.php');
        }

        return isset(self::$_langs[$code]) ? self::$_langs[$code] : $code;
    }

}

?>