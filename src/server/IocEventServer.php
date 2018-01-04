<?php
namespace Xd\PrimeRpc\Server;
use Xd\PrimeRpc\Server\Exceptions\IocException;

/**
 * IOC 事件容器
 * Class IocServer
 * @package Xd\PrimeRpc\Server
 */
class IocEventServer
{
    /**
     *  容器
     *  workerStart - worker启动事件
     *  connect - IO事件connect
     *  receive - IO事件receive
     *  close - IO事件close
     * @var array
     */
    public static $containers = [];

    /**
     * 注入容器
     * @param $key
     * @param $callback
     */
    public static function inject($key, $callback)
    {
        self::$containers[$key] = $callback;
    }

    /**
     * 取出容器中的方法
     * @param $key
     * @return function | false
     */
    public static function get($key)
    {
        if (!isset(self::$containers[$key])) {
            return false;
        }
        return self::$containers[$key];
    }

    /**
     * 运行指定方法
     * @param $key
     * @param $params
     * @return mixed
     * @throws IocException
     */
    public static function run($key, $params = [])
    {
        if (!isset(self::$containers[$key])) {
            throw new IocException("not found {$key}", 101);
        }
        return call_user_func_array(self::$containers[$key], $params);
    }
}