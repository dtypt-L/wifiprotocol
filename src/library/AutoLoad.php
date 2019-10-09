<?php
/**
 * Created by IntelliJ IDEA.
 * User: Bober
 * Email:bober.l@foxmail.com
 * Date: 2019/10/8
 * Time: 20:12
 */

namespace wifi\protocol\library;

class AutoLoad
{
    public function __construct()
    {
        $path = __DIR__ . '/../auto/';
        if (is_file($path)) {
            require_once($path);
        } else if (is_dir($path)) {
            $path = substr($path, -1) === '/' ? $path : $path . '/';
            $p = scandir($path);
            foreach ($p as $val) {
                $filePath = $path . $val;
                if (is_file($filePath)) {
                    require_once($filePath);
                }
            }
        } else {
            throw new \Exception('由于厂商协议升级，本服务不再支持。', 200);
        }
        @ipr_auth();
    }
}