<?php
include __DIR__.'/../../../vendor/autoload.php';

$server = new \Xd\PrimeRpc\Server\TcpServer('test', '0.0.0.0', '9501', 2);

//注入事件回调
\Xd\PrimeRpc\Server\IocEventServer::inject('receive', function($fd, $data){
    //处理事件，只能return array
    $output = ['code' => 0, 'msg' => 'ok', 'result' => ['title' => "test={$data['test']}"]];
    return $output;
});

$server->start();