<?php
/**
 * Created by IntelliJ IDEA.
 * User: Bober
 * Email:bober.l@foxmail.com
 * Date: 2019/9/29
 * Time: 12:53
 */

namespace wifi\protocol\socket;

class UDP
{
    protected $serveIp = null;      //通讯服务器IP地址
    protected $servePort = null;    //通讯服务器端口地址
    protected $serveTimeOut = array("sec" => 2, "usec" => 0);

    public function __construct($serveIp = '127.0.0.1', $servePort = 1812, $serveTimeOut = 2)
    {
        error_reporting(E_ALL);
        set_time_limit(0);
        ob_implicit_flush();
        $this->serveIp = $serveIp;
        $this->servePort = $servePort;
        $this->serveTimeOut = $serveTimeOut;
    }
    public function Client($chrStr = null)
    {

        if (empty($chrStr) || !is_string($chrStr)) {
            throw new \Exception('发送的消息【chrStr】必须是字符串', 203);
        } else {
            $bufStr = $chrStr;
        }
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, $this->serveTimeOut);
        if ($socket === false) {
            throw new \Exception('socket_create():' . socket_strerror(socket_last_error($socket)), 503);
        }
        $send = socket_sendto($socket, $bufStr, strlen($bufStr), 0, $this->serveIp, $this->servePort);
        if ($send === false) {
            throw new \Exception('socket_sendto():' . socket_strerror(socket_last_error($socket)), 503);
        } else {
            $bufStr = socket_read($socket, 1024);
        }
        $data = $bufStr;
        socket_close($socket);
        return $data;
    }

    public function Server($callClass=null,$callAction = null)
    {
        $nowTime = date('Y-m-d H:i:s');
        $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
        if ($socket === false) {
            throw new \Exception('socket_create():' . socket_strerror(socket_last_error($socket)), 503);
        }
        $ok = socket_bind($socket, $this->serveIp, $this->servePort);
        if ($ok === false) {
            throw new \Exception('socket_bind() failed:reason:' . socket_strerror(socket_last_error($socket)), 503);
        }
        while (true) {
            $from = "";
            $port = 0;
            socket_recvfrom($socket, $bufStr, 1024, 0, $from, $port);
            if ($bufStr === false) {
                var_dump("{$nowTime} 服务端:未接收到数据");
            } else {
                $nowTime = date('Y-m-d H:i:s');
                if ($callClass && $callAction) {
                    call_user_func_array(array($callClass, $callAction), [$bufStr, $socket, $from, $port]);
                } else {
                    $repBufStr = "{$nowTime}服务端收到:{$bufStr}";
                    $send = socket_sendto($socket, $repBufStr, strlen($repBufStr), 0, $from, $port);
                    if ($send === false) {
                        var_dump("{$nowTime}服务端发送失败:{$repBufStr}");
                    }
                }
            }
            usleep(1000);
        }
    }
}