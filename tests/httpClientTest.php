<?php
include_once __DIR__.'/../vendor/autoload.php';

use Xd\AsyncHttp\AsyncHttp;

class tcpClientTest extends \PHPUnit_Framework_TestCase {

    public function testOne()
    {
        echo PHP_EOL.'开始测试'.__CLASS__;

        $start = microtime(1);

        //请求一：耗时1秒
        $req = AsyncHttp::get("http://127.0.0.1:9501?test=1&sleepTime=1");
        $req->request();

        //请求二，采用第二个参数传query，耗时1秒
        $req2 = AsyncHttp::get("http://127.0.0.1:9501", ['sleepTime' => 1, 'test' => 2])->request();

        //取回响应数据
        $res = json_decode($req->getResponse()->body, true);
        $res2 = json_decode($req2->getResponse()->body, true);

        $this->assertEquals("1", $res['test']);
        $this->assertEquals("2", $res2['test']);

        //大于等于2s表示是同步
        $costTime = microtime(1) - $start;
        $this->assertLessThan(2, $costTime);

        echo PHP_EOL."测试完成：当前耗时:{$costTime}s".PHP_EOL.PHP_EOL;
    }
}