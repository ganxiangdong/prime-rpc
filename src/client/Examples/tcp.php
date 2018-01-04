<?php
include __DIR__.'/../../../vendor/autoload.php';

//连接
$client = new Xd\PrimeRpc\Client\TcpClient('127.0.0.1', 9501);

//发送请求1
$request1 = $client->request(['test' => '1']);
//发送请求2
$request2 = $client->request(['test' => '2']);

//...

sleep(2);

//取回请求2数据
$res2 = $request2->receive();
print_r($res2);


//取回请求1数据
$res1 = $request1->receive();
print_r($res1);

//请求3，连写
$res3 = $client->request(['test' => '3'])->receive();
print_r($res3);
