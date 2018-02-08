<?php
namespace Xd\PrimeRpc\Client;

class TcpClient
{
    /**
     * 最后一次的请求ID
     * @var int
     */
    private static $lastRequestId = 11;

    /**
     * 响应数据池
     * @var array
     */
    public static $responses = [];

    /**
     * client连接
     * @var swoole_client
     */
    private $swClient;

    /**
     * swoole client 配置
     * @var array
     */
    private static $swConfs = [
        'open_length_check'     => 1,
        'package_length_type'   => 'N',
        'package_length_offset' => 4,       //第N个字节是包长度的值
        'package_body_offset'   => 8,       //第几个字节开始计算长度
        'package_max_length'    => 2097152,  //协议最大长度
    ];

    /**
     * TcpClient constructor.
     * @param $host
     * @param $port
     * @param float $everyIoTimeout 每次IO的超时时间，如每次connect、send、receive 的超时时间
     * @throws ClientException
     */
    public function __construct($host, $port, $everyIoTimeout = 8.0)
    {
        $swClient = new \swoole_client(SWOOLE_SOCK_TCP);
        $swClient->set(self::$swConfs);
        if (!$swClient->connect($host, $port, $everyIoTimeout))
        {
            throw new ClientException("connet {$host}:{$port} is failed", 100);
        }
        $this->swClient = $swClient;
    }

    /**
     * 析构
     */
    public function __destruct()
    {
        $this->swClient->close();
    }

    /**
     * 请求数据
     * @param $requestData
     * @return Request
     */
    public function request($requestData)
    {
        return new Request($this->swClient, $requestData);
    }

    /**
     * 生成 request id
     * @return int
     */
    public static function createRequestId()
    {
        if (self::$lastRequestId > 100000) {
            self::$lastRequestId = 1; //重新从1开始计数
        } else {
            self::$lastRequestId ++;
        }
        return self::$lastRequestId;
    }
}