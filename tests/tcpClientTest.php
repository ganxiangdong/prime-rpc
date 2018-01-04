<?php
include_once __DIR__.'/../vendor/autoload.php';

class tcpClientTest extends \PHPUnit_Framework_TestCase {

    public function testOne()
    {
        echo PHP_EOL.'开始测试'.__CLASS__;
        $start = microtime(1);
        //测试同一个connet，并发请求两次
        $client = new \Xd\PrimeRpc\Client\TcpClient('127.0.0.1', 9501);
        //连续send两个请求
        $req1 = $client->request(['test' => 1]);
        $req2 = $client->request(['test' => 2]);

        //取回响应数据
        $res2 = $req2->receive();
        $res1 = $req1->receive();

        $this->assertEquals("2", $res2['result']['test']);
        $this->assertEquals("1", $res1['result']['test']);

        //总时间小于2秒是才是server和client
        $costTime = microtime(1) - $start;
        $this->assertLessThan(2, $costTime);
        echo PHP_EOL."测试完成：耗时{$costTime}s".PHP_EOL.PHP_EOL;
    }
}