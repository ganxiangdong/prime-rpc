<?php
include_once __DIR__.'/../vendor/autoload.php';

$server = new \Xd\PrimeRpc\Server\TcpServer('test', '0.0.0.0', '9501', 3);

//注入事件回调
\Xd\PrimeRpc\Server\IocEventServer::inject('receive', function($fd, $data){
    //处理事件，只能return array
    sleep(1);
    $output = ['code' => 0, 'msg' => 'ok', 'result' => ['test' => $data['test']]];
    return $output;
});

$server->start();