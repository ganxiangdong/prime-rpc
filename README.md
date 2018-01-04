Prime-rpc is a basal and asynchronous RPC library

### Install

----

composer require xd/prime-rpc

### Examples

---

1. Base on TCP

   Server
   ```php
   <?php
   include 'vendor/autoload.php';
   $server = new \Xd\PrimeRpc\Server\TcpServer('yur server name', '0.0.0.0', '9501', 20);
   
   //bind receive event 
   \Xd\PrimeRpc\Server\IocEventServer::inject('receive', function($fd, $data){
       //do something...
    
       //output content at last
       $output = ['code' => 0, 'msg' => 'ok', 'result' => ['title' => "test={$data['test']}"]];
       return $output;
   });
   $server->start();
   ```
   Client
   ```php
   <?php
   //connect host
   $client = new Xd\PrimeRpc\Client\TcpClient('127.0.0.1', 9501);
    
   //send data
   $request1 = $client->request(['test' => '1']);
   //send data
   $request2 = $client->request(['test' => '2']);
    
   //get the second response data
   $res2 = $request2->receive();
   print_r($res2);
     
   //get the first response data
   $res1 = $request1->receive();
   print_r($res1);

   ```

2. Base on HTTP
   Server
   ```php
   <?php
   include 'vendor/autoload.php';
   $server = new \Xd\PrimeRpc\Server\HttpServer('yur server name', '0.0.0.0', '9501', 20);
      
   //bind request event 
   \Xd\PrimeRpc\Server\IocEventServer::inject('request', function($request, $response){
       //do something...
       
       //output content at last
       $output = ['code' => 0, 'msg' => 'ok', 'result' => ['title' => "test={$request->get['test']}"]];
       return $output;
   });
      $server->start();
   ```
   Client: 
   recommend use the github.com/ganxiangdong/async-http-client
   
3. Manage server    
   shutdown: sh vender/src/server/bins/shutdown.sh        
   reload: sh vender/src/server/bins/reload.sh
   
4. more information please visit github.com/swoole/swoole-src