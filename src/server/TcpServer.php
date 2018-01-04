<?php
namespace Xd\PrimeRpc\Server;

/**
 * Class TcpServer
 * @package Xd\PrimeRpc\Server
 */
class TcpServer extends Server
{
    /**
     * tcp的配置项
     * @var array
     */
    private $tcpConfigs = [
        'heartbeat_idle_time' => 600,
        'heartbeat_check_interval' => 60,
        'open_length_check' => 1,
        'package_length_type' => 'N',
        'package_length_offset' => 4,
        'package_body_offset' => 8,
        'package_max_length' => 2097152, //最大包长
    ];

    public function __construct($serverName, $ip = '0.0.0.0', $port, $workerNum, array $configs = [])
    {
        parent::__construct($serverName, $ip, $port, $workerNum, $configs);
        $this->configs = array_merge($this->configs, $this->tcpConfigs);
    }

    /**
     * 绑定IO事件
     */
    protected function bindIOEvent()
    {
        $this->server->on('connect', '\Xd\PrimeRpc\Server\EventServer::connect');
        $this->server->on('receive', '\Xd\PrimeRpc\Server\EventServer::receive');
        $this->server->on('close', '\Xd\PrimeRpc\Server\EventServer::close');
    }

    /**
     * 创建server
     */
    protected function createServer()
    {
        $this->server = new \swoole_server($this->ip, $this->port, SWOOLE_PROCESS);
    }
}