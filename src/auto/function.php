<?php
/**
 * Created by IntelliJ IDEA.
 * User: Bober
 * Email:bober.l@foxmail.com
 * Date: 2019/9/27
 * Time: 16:48
 */

//------------------------
// 助手函数
//-------------------------


/**
 * 异常捕获
 * @param $msg
 * @param int $code
 * @param string $exception
 */
function p_exception($msg, $code = 0, $exception = '')
{
    throw new \Exception($msg, $code);

}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装）
 * @return mixed
 */
function get_client_ip($type = 0, $adv = true)
{
    $type = $type ? 1 : 0;
    static $ip = NULL;
    if ($ip !== NULL) return $ip[$type];
    if ($adv) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
    } elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

function get_public_ip($type = 0)
{
    $type = $type ? 1 : 0;
    //获取外网IP的地址，以下两个均测试成功
    $url = 'http://tool.huixiang360.com/zhanzhang/ipaddress.php';
//        $url = 'http://city.ip138.com/ip2city.asp';
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $a = curl_exec($ch);
    preg_match('/\[(.*)\]/', $a, $ipArr);
    $ip = $ipArr[1];
    // IP地址合法验证
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 获取目录的结构
 * @author bober
 * @param  [string] $path [目录路径]
 * @return [array]       [目录结构数组]
 */
function dir_tree($path = __DIR__)
{
    $handle = opendir($path);
    $itemArray = array();
    while (false !== ($file = readdir($handle))) {
        if (($file == '.') || ($file == '..')) {
        } elseif (is_dir($path . $file)) {
            try {
                $dirtmparr = dir_tree($path . $file . '/');
            } catch (Exception $e) {
                $dirtmparr = null;
            };
            $itemArray[$file] = $dirtmparr;
        } else {
            array_push($itemArray, $file);
        }
    }
    return $itemArray;
}

//递归生成权限的文件目录
function dir_create($dir, $mode = 0777)
{
    if (!@mkdir($dir, $mode)) {
        $sundir = dir_create($dir);
        create_dir($sundir, $mode);
    }
    @mkdir($dir, $mode);
}


/**
 * 浏览器友好的变量输出
 * @param mixed $var 变量
 * @param boolean $echo 是否输出 默认为True 如果为false 则返回输出字符串
 * @param string $label 标签 默认为空
 * @param boolean $strict 是否严谨 默认为true
 * @return void|string
 */
function p_dump($var, $echo = true, $label = null, $strict = true)
{
    $label = ($label === null) ? '' : rtrim($label) . ' ';
    if (!$strict) {
        if (ini_get('html_errors')) {
            $output = print_r($var, true);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        } else {
            $output = $label . print_r($var, true);
        }
    } else {
        ob_start();
        var_dump($var);
        $output = ob_get_clean();
        if (!extension_loaded('xdebug')) {
            $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
        }
    }
    if ($echo) {
        echo($output);
        return null;
    } else
        return $output;
}