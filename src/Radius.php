<?php
/**
 * Created by IntelliJ IDEA.
 * User: Bober
 * Email:bober.l@foxmail.com
 * Date: 2019/9/27
 * Time: 16:48
 */

namespace wifi\protocol;

use wifi\protocol\library\AutoLoad;
use wifi\protocol\Socket;

class Radius
{
    protected $radiusBytes = array();       //Radius协议字节数组
    protected $radiusArr = array();        //Radius协议数组
    protected $radiusAttrArr = array();    //Radius协议报文属性数组

    public function __construct($userIp = null)
    {
        $this->radiusArr = array(
            'code' => 20,
            'identifier' => mt_rand(1, 126),
            'length' => shortToBytes(20),
            'authenticator' => $this->getAauthenticator(),
            'attrArr' => array(
                20 => $userIp
            ),
        );
    }

    /**
     * 设置Radius协议数组
     * @param string $key
     * @param string $val
     * @return array
     * @throws \Exception
     */
    public function setRadius($key = '', $val = '')
    {
        if (is_array($key)) {
            foreach ($key AS $k => &$v) {
                $this->setRadius($k, $v);
            }
        } else {
            if (!$key) {
                throw new \Exception('key:键不能为空', 203);
            } else if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $key)) {
                throw new \Exception("radiusArr报文不能包含中文字符：key={$key}", 203);
            } else if ($key != 'attrArr' && !array_key_exists($key, $this->radiusArr)) {
                throw new \Exception("radiusArr报文无[{$key}]字段", 203);
            } else if (!$val && $val === '') {
                throw new \Exception($key . 'val:值不能为空', 203);
            } else if (gettype($val) === 'string' && preg_match('/[\x{4e00}-\x{9fa5}]/u', $val)) {
                throw new \Exception("radiusArr报文不能包含中文字符：key={$val}", 203);
            } else if ($key === 'attrArr' && is_array($val)) {
                foreach ($val AS $k => &$v) {
                    $this->setRadiusAttrArr($k, $v);
                }
            } else {
                $this->radiusArr[$key] = $val;
            }
        }
        return $this;
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