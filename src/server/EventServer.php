<?php
namespace Xd\PrimeRpc\Server;


use Xd\PrimeRpc\Server\Exceptions\IocException;

class EventServer
{
    /**
     * connect 事件
     * @param $server
     * @param $fd
     */
    public static function connect($server, $fd)
    {
        try {
            IocEventServer::run('connect', [$server, $fd]);
        } catch (IocException $e){}
    }

    /**
     * receive 事件
     * @param $server
     * @param $fd
     * @param $reactorId
     * @param $data
     */
    public  static function receive($server, $fd, $reactorId, $data)
    {
        var_dump($server->worker_pid.'=');
        //拆包
        $header = unpack('N', substr($data,0, 4));

        if (empty($header[1]) || $header[1] <= 0) {
            //不合法的请求数据,没有requestId
            return;
        }
        $requestId = $header[1];
        $rawBody = substr($data, 8);
        $body = json_decode($rawBody, 1);
        try {
            //调用业务方法
            $body = IocEventServer::run('receive', [$fd, $body]);
            if (!is_array($body)) {
                throw new IocException('return data type is only allow array', 102);
            }
            //包装固定头协议
            $bodyJson = json_encode($body, JSON_UNESCAPED_UNICODE);
            $bodyWrap = pack('NN', $requestId, strlen($bodyJson)).$bodyJson;
            $server->send($fd, $bodyWrap);
        } catch (IocException $e){//没有定义回调函数
        }
    }

    /**
     * close 事件
     * @param $server
     * @param $fd
     */
    public  static function close($server, $fd)
    {
        try {
            IocEventServer::run('close', [$server, $fd]);
        } catch (IocException $e){}
    }

    /**
     * HTTP 收到请求事件
     * @param $request
     * @param $response
     */
    public static function request($request, $response)
    {
        try {
            $body = IocEventServer::run('request', [$request, $response]);
            if ($body !== '' && !is_array($body)) {
                throw new IocException('return data type is only allow array or empty string', 103);
            }
            $body = json_encode($body, JSON_UNESCAPED_UNICODE);
            $response->header('Content-Type','application/json;charset=UTF-8');
            $response->end($body);
        } catch (IocException $e){}
    }
}