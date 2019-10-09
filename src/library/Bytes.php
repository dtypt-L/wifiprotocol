<?php
/**
 * Created by IntelliJ IDEA.
 * User: Bober
 * Email:bober.l@foxmail.com
 * Date: 2019/9/27
 * Time: 16:48
 */

namespace wifi\protocol\library;

/**
 * byte数组与字符串转化类
 * @author Bober
 * @email bober.l@foxmail.com
 * Created on 2019-09-26
 */
class Bytes
{
    /**
     * 将字节数组转化为String类型的数据
     * @param $bytes 字节数组
     * @param $str 目标字符串
     * @return 一个String类型的数据
     */
    public static function strToArray($str, $in_charset = 'utf-8', $out_charset = 'utf-8')
    {
        if (gettype($str) != 'string') {
            $data = false;
        } else {
            $str = iconv($in_charset, $out_charset, $str);
            preg_match_all('/(.)/u', $str, $matches);
            $data = $matches[1];
        }
        return $data;
    }

    public static function arrayToStr($array, $in_charset = 'utf-8', $out_charset = 'utf-8')
    {
        if (!is_array($array)) {
            $data = false;
        } else {
            $data = '';
            array_walk_recursive($array, function ($value) use (&$data) {
                $data .= $value;
            });
            $data = iconv($in_charset, $out_charset, $data);
        }
        return $data;
    }

    /**
     * @param $array
     * @param bool $valueIsStr
     * @return array|bool
     */
    public static function arrayValueToBytes($array, $valueIsStr = true, $in_charset = 'utf-8', $out_charset = 'utf-8')
    {
        if (!is_array($array)) {
            $data = false;
        } else {
            $arr = array();
            array_walk_recursive($array, function ($value) use (&$arr) {
                array_push($arr, $value);
            });
            $data = $arr;
            foreach ($data AS &$value) {
                if ($valueIsStr) {
                    $value = iconv($in_charset, $out_charset, $value);
                    $value = unpack('C*', $value);
                } else {
                    $value = $value & 0xff;
                }
            }

        }
        return $data;
    }

    /**
     * 统计多维数组元素个数
     * @param $array
     * @return int|string
     */
    public static function arrCount($array)
    {
        if (!is_array($array)) {
            $data = strstr($array);
        } else {
            $data = 0;
            array_walk_recursive($array, function ($value) use (&$data) {
                $data++;
            });
        }
        return $data;
    }
    /**
     * 打包TLV成报文
     * @param $array
     * @return array
     */
    public static function encodeArrTLV($array, $type = false)
    {
        $data = array();
        $n = 0;
        foreach ($array AS $key => $value) {
            if ($value !== null) {
                $value = unpack('C*', $value);
                $data[$key]['type'] = $type ? $key : ++$n;
                $data[$key]['len'] = count($value) + 2;
                $data[$key]['value'] = $value;
            }
        }
        return $data;
    }

    /**
     * 解包TLV报文
     * @param $array
     * @return array|bool
     */
    public static function decodeArrTLV($array)
    {
        if (!count($array) > $array[0]) {
            $data = false;
        } else {
            $data = array();
            for ($i = $array[0] == 1 ? 0 : 1; $i < count($array); $i++) {
                $n = $array[$i];
                $len = $array[++$i] - 2;
                $arr = array_slice($array, $n + 2, $len);
                $value = '';
                foreach ($arr AS $val) {
                    $value .= pack('C*', $val);
                }
                $data[] = $value;
                $i += $len;
            }
        }

        return $data;
    }

    /**
     * 转换一个String字符串为byte数组
     * @param $str 需要转换的字符串
     * @param $bytes 目标byte数组
     * @author Zikie
     */

    public static function getBytes($str)
    {

        $len = strlen($str);
        $bytes = array();
        for ($i = 0; $i < $len; $i++) {
            if (ord($str[$i]) >= 128) {
                $byte = ord($str[$i]) - 256;
            } else {
                $byte = ord($str[$i]);
            }
            $bytes[] = $byte;
        }
        return $bytes;
    }

    /**
     * 将字节数组转化为String类型的数据
     * @param $bytes 字节数组
     * @param $str 目标字符串
     * @return 一个String类型的数据
     */

    public static function toStr($bytes)
    {
        $str = '';
        foreach ($bytes as $ch) {
            $str .= chr($ch);
        }
        return $str;
    }

    /**
     * 转换一个int为byte数组
     * @param $byt 目标byte数组
     * @param $val 需要转换的字符串
     * @author Zikie
     */

    public static function integerToBytes($val)
    {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);    //   >>：移位    &：与位
        $byt[2] = ($val >> 16 & 0xff);
        $byt[3] = ($val >> 24 & 0xff);
        return $byt;
    }

    /**
     * 从字节数组中指定的位置读取一个Integer类型的数据
     * @param $bytes 字节数组
     * @param $position 指定的开始位置
     * @return 一个Integer类型的数据
     */

    public static function bytesToInteger($bytes, $position)
    {
        $val = 0;
        $val = $bytes[$position + 3] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 2] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position + 1] & 0xff;
        $val <<= 8;
        $val |= $bytes[$position] & 0xff;
        return $val;
    }

    /**
     * 转换一个shor字符串为byte数组
     * @param $byt 目标byte数组
     * @param $val 需要转换的字符串
     * @author Zikie
     */

    public static function shortToBytes($val)
    {
        $byt = array();
        $byt[0] = ($val & 0xff);
        $byt[1] = ($val >> 8 & 0xff);
        return $byt;
    }

    /**
     * 从字节数组中指定的位置读取一个Short类型的数据。
     * @param $bytes 字节数组
     * @param $position 指定的开始位置
     * @return 一个Short类型的数据
     */

    public static function bytesToShort($bytes, $position)
    {
        $val = 0;
        $val = $bytes[$position + 1] & 0xFF;
        $val = $val << 8;
        $val |= $bytes[$position] & 0xFF;
        return $val;
    }
}