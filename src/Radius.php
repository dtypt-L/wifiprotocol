<?php
/**
 * Created by IntelliJ IDEA.
 * User: Bober
 * Email:bober.l@foxmail.com
 * Date: 2019/9/27
 * Time: 16:48
 */

namespace wifi\protocol;

use wifi\protocol\Socket;

class Radius extends Base
{
    protected $protocol = 'RADIUS';        //协议名称
    protected $isPAP = true;                 //是否PAP认证
    protected $radius = '';                  //radius协议
    protected $serveIp = '127.0.0.1';         //通讯服务器IP地址
    protected $servePort = 1812;              //通讯服务器端口地址
    protected $serveProtocol = 'UDP';          //通讯服务器协议
    protected $serveTimeOut = 2;

    /**
     * Radius constructor.
     * 初始化协议
     * @param int $isPAP PAP认证=1 CHAP认证=0
     * @param string $serveIp 用户IP
     * @param int $servePort
     * @param string $serveProtocol
     * @param int $serveTimeOut
     * @throws \Exception
     */
    public function __construct($isPAP = 1, $serveIp = '127.0.0.1', $servePort = 2000, $serveProtocol = 'UDP', $serveTimeOut = 2)
    {

        $class=explode('\\',__CLASS__);
        $className=strtoupper(end($class));
        $this->protocol=$className;
        $this->serveIp = $serveIp;
        $this->servePort = $servePort;
        $this->serveProtocol = $serveProtocol;
        $this->serveTimeOut = $serveTimeOut;
        $this->isPAP = $isPAP ? 1 : 0;
        parent::__construct($className);
        $userIp = get_client_ip(0, true);
        if ($userIp!=='0.0.0.0'){
            $this->setRadius(7, $userIp);
        }
        $this->setRadius(2, $this->isPAP);
    }

    /**
     * 设置协议
     * @param string $key 协议属性
     * @param string $val 协议属性值
     * @return $this
     * @throws \Exception
     */
    public function setRadius($key = '', $val = '')
    {
        if (is_array($key)) {
            foreach ($key AS $k => &$v) {
                $this->setRadius($k, $v);
            }
        } else {
            $this->setProtocol($key, $val);
        }
        return $this;
    }

    /**
     * 获取协议
     * @param bool $TLV 是否包含TLV 默认否
     * @param bool $isChrStr 是否是字符串 默认是
     * @return array|string
     */
    public function getRadius($TLV = false, $isChrStr = true)
    {
        $this->radius = $this->getProtocol($TLV, $isChrStr);
        return $this->radius;
    }

    /**
     * 得到协议回复对应消息
     * @param int $type 协议类型
     * @param int $code 协议类型的CODE
     * @return mixed
     */
    public function getRadiusErrMsg($type = 0, $code = 0)
    {
        return $this->getProtocolMsg($type, $code);
    }


    protected function getAauthenticator()
    {
        $data = array();
        for ($i = 0; $i < 16; $i++) {
            $data[$i] = mt_rand(1, 126);
        }
        return $data;
    }

}