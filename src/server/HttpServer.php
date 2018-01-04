<?php
namespace Xd\PrimeRpc\Server;

/**
 * Class HttpServer
 * @package Xd\PrimeRpc\Server
 */
class HttpServer extends Server
{
    /**
     * 创建server
     */
    protected function createServer()
    {
        $this->server = new \swoole_http_server($this->ip, $this->port);
    }

    /**
     * 绑定IO事件
     */
    protected function bindIOEvent()
    {
        $this->server->on('request', '\Xd\PrimeRpc\Server\EventServer::request');
    }
}