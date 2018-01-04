<?php
namespace Xd\PrimeRpc\Server;

use Xd\PrimeRpc\Server\Exceptions\IocException;

abstract class Server
{
    /**
     * 服务名
     * @var string
     */
    protected $serverName;

    /**
     * 监听IP
     * @var string
     */
    protected $ip = '0.0.0.0';

    /**
     * 监听端口
     * @var int
     */
    protected $port;

    /**
     * swoole server configs
     * @var array
     */
    protected $configs = [
        'worker_num' => 10,
        'max_request' => 1000,
        'dispatch_mode' => 3,
        'daemonize' => 0,
        'log_file' => '/var/log/swoole.log',
    ];

    /**
     * Server根目录
     * @var string
     */
    protected $rootPathServer;

    /**
     * server 对象
     * @var object
     */
    protected $server;

    /**
     * TcpServer constructor.
     * @param $serverName
     * @param $ip
     * @param $port
     * @param $workerNum
     * @param array $configs
     */
    public function __construct($serverName, $ip = '0.0.0.0', $port, $workerNum, $configs = [])
    {
        $this->serverName = $serverName;
        $this->ip = $ip;
        $this->port = $port;
        $this->configs['worker_num'] = $workerNum;
        $this->configs = array_merge($this->configs, $configs);

        $this->rootPathServer = __DIR__;
    }

    /**
     * 绑定IO事件
     * @return null
     */
    abstract protected function bindIOEvent();

    /**
     * 创建server
     * @return null
     */
    abstract protected function createServer();

    /**
     * 启动server
     */
    public function start()
    {
        //创建server
        $this->createServer();

        //设置server
        $this->server->set($this->configs);

        //绑定IO事件
        $this->bindIOEvent();

        //swoole的一些其它事件绑定
        $this->server->on('start', [$this, 'onStart']); //主进程启动事件
        $this->server->on('workerStart', [$this, 'onWorkerStart']); //worker启动事件

        //启动
        $this->server->start();
    }

    /**
     * 主进程启动回调函数
     */
    public function onStart()
    {
        //设置主进程别名
        $processName = 'swoole_manager_' . $this->serverName;
        swoole_set_process_name($processName);

        //生成重启的shell脚本
        $reload = "echo 'Reloading...'\n";
        $reload .= "pid=$(pidof {$processName})\n";
        $reload .= "kill -USR1 \"\$pid\"\n";
        $reload .= "echo 'Reloaded'\n";
        $filePath = $this->rootPathServer . '/bins/reload.sh';
        file_put_contents($filePath, $reload);

        //生成关闭shell脚本
        $shutdown = "echo 'shutdown...'\n";
        $shutdown .= "pid=$(pidof {$processName})\n";
        $shutdown .= "kill -15 \"\$pid\"\n";
        $shutdown .= "echo 'done'\n";
        $filePath = $this->rootPathServer . '/bins/shutdown.sh';
        file_put_contents($filePath, $shutdown);

        //运行注入的事件，如果存在的话
        try {
            IocEventServer::run('start');
        } catch (IocException $e){}
    }

    /**
     * 子进程启动事件
     * @param $server
     * @param $workerId
     */
    public function onWorkerStart($server, $workerId)
    {
        //设置worker进程名称
        swoole_set_process_name('swoole_' .$this->serverName .'_worker_' .$workerId);

        //运行注入的事件，如果存在的话
        try {
            IocEventServer::run('workerStart');
        } catch (IocException $e){}
    }

    /**
     * 获取服务名
     * @return string
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * 获取IP
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * 获端口号
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }
}