<?php
namespace Xd\PrimeRpc\Client;

/**
 * Class Request
 * @package Xd\PrimeRpc\TcpClient
 */
class Request
{
    /**
     * 请求ID
     * @var
     */
    private $requestId;

    /**
     * 连接对象
     * @var
     */
    private $swClient;

    /**
     * Request constructor.
     * @param $swClient
     * @param $requestData
     * @throws ClientException
     */
    public function __construct($swClient, array $requestData = [])
    {
        if (!is_array($requestData)) {
            throw new ClientException('request data type is only allow array', 101);
        }

        $this->requestId = TcpClient::createRequestId();
        $this->swClient = $swClient;

        //send数据
        $requestDataJson = json_encode($requestData, JSON_UNESCAPED_UNICODE);
        $wrapRequest = pack('NN', $this->requestId, strlen($requestDataJson)).$requestDataJson;
        $this->swClient->send($wrapRequest);
    }

    /**
     * 接收数据
     * @return mixed
     * @throws ClientException
     */
    public function receive()
    {
        if (!isset(TcpClient::$responses[$this->requestId])) {
            while (true) {
                $rawResContent = $this->swClient->recv();
                if (empty($rawResContent)) {
                    if ($rawResContent === '') {
                        throw new ClientException("recv exception: receive empty string, maybe connect has closed", 103);
                    }
                    throw new ClientException("recv exception: receive FALSE, errorCode: {$this->swClient->errCode}", 104);
                }
                $resHeader = unpack('N', substr($rawResContent, 0, 4));
                if (empty($resHeader[1]) || $resHeader[1] <= 0) {
                    throw new ClientException("service exception: not found requestId in response content，response conetent '{$rawResContent}'", 102);
                }
                $requestId = $resHeader[1];
                $rawBody = substr($rawResContent, 8);
                $body = json_decode($rawBody, true);
                TcpClient::$responses[$requestId] = $body;
                if ($requestId == $this->requestId) {
                    break;
                }
            }
        }
        return TcpClient::$responses[$this->requestId];
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        //清空响应数据记录
        if (isset(TcpClient::$responses[$this->requestId])) {
            unset(TcpClient::$responses[$this->requestId]);
        }
    }
}