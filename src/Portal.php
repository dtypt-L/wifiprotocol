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
class Portal extends Base
{
    protected $protocol = 'PORTAL';        //协议名称
    protected $isPAP = true;                 //是否PAP认证
    protected $portal = '';                  //portal协议
    protected $serveIp = '127.0.0.1';         //通讯服务器IP地址
    protected $servePort = 2000;              //通讯服务器端口地址
    protected $serveProtocol = 'UDP';          //通讯服务器协议
    protected $serveTimeOut = 2;

    /**
     * Portal constructor.
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
            $this->setPortal(7, $userIp);
        }
        $this->setPortal(2, $this->isPAP);
    }

    /**
     * 设置协议
     * @param string $key 协议属性
     * @param string $val 协议属性值
     * @return $this
     * @throws \Exception
     */
    public function setPortal($key = '', $val = '')
    {
        if (is_array($key)) {
            foreach ($key AS $k => &$v) {
                $this->setPortal($k, $v);
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
    public function getPortal($TLV = false, $isChrStr = true)
    {
        $this->portal = $this->getProtocol($TLV, $isChrStr);
        return $this->portal;
    }

    /**
     * 得到协议回复对应消息
     * @param int $type 协议类型
     * @param int $code 协议类型的CODE
     * @return mixed
     */
    public function getPortalErrMsg($type = 0, $code = 0)
    {
        return $this->getProtocolMsg($type, $code);
    }

    /**
     * Potal认证
     * @param null $IP 服务端IP
     * @param null $userIp 用户IP
     * @param null $userName 用户账号
     * @param null $userPass 用户密码
     * @param bool $isPAP 认证类型
     * @return array|bool
     * @throws \Exception
     */
    public function onLine($userIp = null, $userName = null, $userPass = null, $isPAP = true)
    {
        if ($userIp) {
            $this->setPortal('userIp', $userIp);
        }
        if (!$userName) {
            throw new \Exception("userName不能为空", 203);
        } else if (!$userPass) {
            throw new \Exception("userPass不能为空", 203);
        } else {
            $this->setPortal(5, array(mt_rand(1, 126), mt_rand(1, 126)))
                ->setPortal(6, array(mt_rand(1, 126), mt_rand(1, 126)))
                ->setPortal(11, array('1' => $userName, '2' => $userPass));
        }
        if (!$isPAP || !$this->isPAP) {
            $this->setPortal(2, 1)
                ->setPortal(3, 0);
            $portal = $this->getPortal(false, true);
            $data = $this->send($portal);
            $type = $data[2];
            $code = $data[15];
            if (!$data || $type !== 2) {
                throw new \Exception("请求响应错误；", 205);
            } else if ($code) {
                $msg = $this->getPortalErrMsg($type, $code);
                throw new \Exception($msg ? $msg : "请求错误；", 200 + $code);
            } else {
                $this->setPortal('serialNo', array_slice($data, 4, 2));
                $this->setPortal('reqId', array_slice($data, 6, 2));
                $attrArr = Bytes::decodeArrTLV(array_slice($data, 15, -1));
                if ($attrArr) {
                    $this->setPortal('attrArr', $attrArr);
                }
            }
        }

        //上线
        $this->setPortal(2, 3);
        $portal = $this->getPortal(true, true);
        $data = $this->send($portal);
        $type = $data[2];
        $code = $data[15];
        if (!$data || $type !== 4) {
            throw new \Exception("认证握手错误；", 205);
        } else if ($code) {
            $msg = $this->getPortalErrMsg($type, $code);
            throw new \Exception($msg ? $msg : "认证握手响应错误；", 200 + $code);
        }
        //确定
        $this->setPortal(2, 7);
        $portal = $this->getPortal(false, true);
        $this->send($portal);
        return $data;
    }
    /**
     * 下线
     * @param null $userIp
     * @param bool $isPAP
     * @return array|bool
     * @throws \Exception
     */
    public function offLine($userIp = null, $isPAP = true)
    {
        if ($userIp) {
            $this->setPortal(7, $userIp);
        }
        $this->setPortal(5, array(mt_rand(1, 126), mt_rand(1, 126)))
            ->setPortal(6, array(mt_rand(1, 126), mt_rand(1, 126)));
        if (!$isPAP || !$this->isPAP) {
            $this->setPortal(2, 1)
                ->setPortal(3, 0);
            $portal = $this->getPortal();
            $data = $this->send($portal);
            $type = $data[2];
            $code = $data[15];
            if (!$data || $type !== 2) {
                throw new \Exception("请求响应错误；", 205);
            } else if ($code) {
                $msg = $this->getPortalErrMsg($type, $code);
                throw new \Exception($msg ? $msg : "请求错误；", 200 + $code);
            } else {
                $this->setPortal(5, array_slice($data, 4, 2));
                $this->setPortal(6, array_slice($data, 6, 2));
                $attrArr = Bytes::decodeArrTLV(array_slice($data, 15, -1));
                if ($attrArr) {
                    $this->setPortal('attrArr', $attrArr);
                }
            }
        }
        //下线
        $this->setPortal(2, 5);
        $portal = $this->getPortal();
        $data = $this->send($portal);
        $type = $data[2];
        $code = $data[15];
        if (!$data || $type !== 6) {
            throw new \Exception("下线请求错误；", 205);
        } else if ($code) {
            $msg = $this->getPortalErrMsg($type, $code);
            throw new \Exception($msg ? $msg : "下线请求响应错误；", 200 + $code);
        }
        return $data;
    }

    /**
     * 询问
     * @param null $userIp
     * @param bool $isPAP
     * @return array|bool
     * @throws \Exception
     */
    public function inQuiry($userIp = null, $isPAP = true)
    {
        if ($userIp) {
            $this->setPortal(7, $userIp);
        }
        $this->setPortal(5, array(mt_rand(1, 126), mt_rand(1, 126)))
            ->setPortal(6, array(mt_rand(1, 126), mt_rand(1, 126)));
        if (!$isPAP || !$this->isPAP) {
            $this->setPortal(2, 1)
                ->setPortal(3, 0);
            $portal = $this->getPortal();
            $data = $this->send($portal);
            $type = $data[2];
            $code = $data[15];
            if (!$data || $type !== 2) {
                throw new \Exception("请求响应错误；", 205);
            } else if ($code) {
                $msg = $this->getPortalErrMsg($type, $code);
                throw new \Exception($msg ? $msg : "请求错误；", 200 + $code);
            } else {
                $this->setPortal('serialNo', array_slice($data, 4, 2));
                $this->setPortal('reqId', array_slice($data, 6, 2));
                $attrArr = array_slice($data, 15, -1);
                var_dump($attrArr);
                if ($attrArr) {
//                    $this->setPortal('attrArr', $attrArr);/
                }
            }
        }
        //询问
        $this->setPortal('type', 9);
        $portal = $this->getPortal();
        $data = $this->send($portal);
        $type = $data[2];
        $code = $data[15];
        var_dump($data);
        if (!$data || $type !== 10) {
            throw new \Exception("询问请求错误；", 205);
        } else if ($code) {
            $msg = $this->getPortalErrMsg($type, $code);
            throw new \Exception($msg ? $msg : "询问请求响应错误；", 200 + $code);
        } else {
            var_dump($data);
        }
        return $data;
    }

    /**
     * 发送报文
     * @param null $chrStr
     * @return array|bool
     * @throws \Exception
     */
    public function send($chrStr = null)
    {
        $data = false;
        $chrStr = $chrStr ? $chrStr : $this->portal;
        if (empty($chrStr) || !is_string($chrStr)) {
            throw new \Exception('发送的消息【chrStr】必须是字符串', 203);
        } else {
            $initSocket = array(
                'serveIp' => $this->serveIp,
                'servePort' => $this->servePort,
                'serveProtocol' => $this->serveProtocol,
                'serveTimeOut' => $this->serveTimeOut
            );
            $Socket = new Socket();
            $buf = $Socket->getInstance($initSocket)->Client($chrStr);
            if ($buf) {
                $data = unpack('C*', $buf);
            }
        }
        return $data;
    }

    /**
     * 后台进程  MAC无感知认证
     * @param string $callClass
     * @param string $callAction
     * @return mixed
     * @throws \Exception
     */
    public function listen($callClass = "wifi\\protocol\\Portal", $callAction = "callBackFun")
    {
        $initSocket = array(
            'serveIp' => $this->serveIp,
            'servePort' => $this->servePort,
            'serveProtocol' => $this->serveProtocol,
            'serveTimeOut' => $this->serveTimeOut
        );
        $Socket = new Socket();
        return $Socket->getInstance($initSocket)->Server($callClass, $callAction);
    }

    /**
     * 后台经常默认回调
     * @param null $input
     * @param null $socket
     * @param null $serveIp
     * @param null $servePort
     * @return array|bool
     * @throws \Exception
     */
    public static function callBackFun($input = null, $socket = null, $serveIp = null, $servePort = null)
    {
        return self::onLineMac($input, $socket, $serveIp, $servePort);
    }

    /**
     * MAC无感知认证处理
     * @param null $input
     * @param null $socket
     * @param null $serveIp
     * @param null $servePort
     * @return array|bool
     * @throws \Exception
     */
    public static function onLineMac($input = null, $socket = null, $serveIp = null, $servePort = null)
    {
        if (empty($input)) {
            throw new \Exception('无感知MAC认证报文不能为空', 203);
        } else if (empty($socket)) {
            throw new \Exception('无感知MAC认证socket不能为空', 203);
        } else if (empty($serveIp)) {
            throw new \Exception('无感知MAC认证serveIp不能为空', 203);
        } else if (!ip2long($serveIp)) {
            throw new \Exception('无感知MAC认证serveIp不是有效的IP ', 203);
        } else if (empty($servePort)) {
            throw new \Exception('无感知MAC认证servePort不能为空 ', 203);
        } else {
            if (!is_array($input)) {
                $input = unpack('C*', $input);
            }
            if (count($input) < 19) {
                throw new \Exception('无感知MAC认证servePort不能为空 ', 203);
            } else {
                $isPAP = (int)$input[3];
                $bufArr = array_slice($input, 0, 16, true);
                $bufArr[2] += 1;
                $bufArr[15] = 0;
                $bufArr[16] = 0;
                $bufStr = '';
                array_walk_recursive($bufArr, function ($val) use (&$bufStr) {
                    $bufStr .= pack('C*', $val & 0xff);
                });
                $userip = implode('.', array_slice($input, 8, 4));
                $usernameArr = array_slice($input, 18, $input[18] - 2);
                $username = '';
                array_walk_recursive($usernameArr, function ($val) use (&$username) {
                    $username .= pack('C*', $val & 0xff);
                });
                $userpass = $username;
                socket_sendto($socket, $bufStr, strlen($bufStr), 0, $serveIp, $servePort);
            }
            $Portal = new Portal($isPAP, $serveIp, $servePort);
            $data = $Portal->onLine($userip, $username, $userpass);
            return $data;
        }
        return false;
    }

}