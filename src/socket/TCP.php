<?php
/**
 * Created by IntelliJ IDEA.
 * User: Bober
 * Email:bober.l@foxmail.com
 * Date: 2019/9/29
 * Time: 12:53
 */

namespace wifi\protocol\socket;

class TCP
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

    public function Client($massege = '')
    {

    }

    public function Server($brack = '')
    {

    }

}