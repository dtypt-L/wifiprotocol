<?php
/**
 * Created by IntelliJ IDEA.
 * User: Bober
 * Email:bober.l@foxmail.com
 * Date: 2019/9/29
 * Time: 12:53
 */

namespace wifi\protocol;
class Socket
{
    public $initSocket = array(
        'serveIp' => '127.0.0.1',  //通讯服务器IP
        'servePort' => 2000, //通讯服务器端口
        'serveTimeOut' => array("sec" => 2, "usec" => 0), //通讯服务器超时配置
        'serveProtocol' => 'UDP', //通讯服务器协议
    );
    protected $protocol = array('UDP', 'TCP');         //通讯服务器协议

    public function getInstance(array $initSocket = null)
    {
        if ($initSocket && !is_array($initSocket)) {
            $msg = "请初始化包含：serveIp、servePort、serveProtocol、serveTimeOut的数组";
            throw new \Exception($msg, 203);
        } else {
            $this->initSocket = array_merge($this->initSocket, $initSocket);
            $protocolKey=array_search(strtoupper($this->initSocket['serveProtocol']),$this->protocol);
        }
        if (empty($this->initSocket['serveIp'])) {
            throw new \Exception('请初始化serveIp', 203);
        } else if (!ip2long($this->initSocket['serveIp'])) {
            throw new \Exception('初始化serveIp格式不对', 203);
        } else if (empty($this->initSocket['servePort'])) {
            throw new \Exception('请初始化servePort', 203);
        } else if (empty($this->initSocket['serveProtocol'])) {
            throw new \Exception('请初始化serveProtocol', 203);
        } else if (!$protocolKey && $protocolKey !== 0) {
            throw new \Exception("暂未提供{$this->initSocket['serveProtocol']}的支持", 203);
        } else if (empty($this->initSocket['serveTimeOut'])) {
            throw new \Exception('请初始化serveTimeOut', 203);
        } else {
            if (!is_array($this->initSocket['serveTimeOut'])) {
                $this->initSocket['serveTimeOut'] = array("sec" => (int)$this->initSocket['serveTimeOut'], "usec" => 0);
            }
            $this->initSocket['servePort'] = (int)$this->initSocket['servePort'];
            $className = "\\wifi\\protocol\\socket\\{$this->initSocket['serveProtocol']}";
            $protocolClass = new \ReflectionClass($className);
            unset($this->initSocket['serveProtocol']);
            return $protocolClass->newInstanceArgs($this->initSocket);
        }

    }
}