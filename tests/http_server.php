<?php
include __DIR__.'/../vendor/autoload.php';

$server = new \Xd\PrimeRpc\Server\HttpServer('test', '0.0.0.0', '9501', 2);

\Xd\PrimeRpc\Server\IocEventServer::inject('request', function ($request, $response) {
    //请不要自行$response->end，请return 后交上上层end，只能返回一个数组
    sleep($request->get['sleepTime']);
        $body = ['test' => $request->get['test']];
    return $body;
});

$server->start();