WIFI-Protocol
===============

WIFI-Protocol 是提供WIFI以Web portal方式提供身份验证，授权，记帐和审计服务
 + 认证、授权、计费、审计（authentication、authorization、accounting、auditing）
 + 实现portal1.0+2.0协议
 + 实现Radius1.0+2.0协议

> 的运行环境要求LAMP/LNMP（Linux>=6.4 Apache>=2.2 Nginx>=1.14 Mysql>=5.6 PHP>=5.4）
详细开发文档参考 [AAAA-WIFI完全开发手册](https://github.com/dtypt-L/wifiprotocol/wikis)

<hr>

## 一、使用方法

### 1.1、安装

~~~
 composer require dtypt/wifi-protocol ~1.0.0-beta
~~~
### 1.2、开发文档

1.2.1、[github地址](https://github.com/dtypt-L/wifiprotocol)
~~~
github.com/dtypt-L/wifiprotocol
~~~
### 1.3、使用方法

1.3.1、Portal协议


~~~
//初始化
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
 $Portal = new Portal(1,$nasip,2000);
 
//上线
 $data = $Portal->online($userip, $username, $userpass);
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
      
 //下线   
  $data=$Portal->offline($userip);  
  /**
    * 下线
    * @param null $userIp
    * @param bool $isPAP
    * @return array|bool
    * @throws \Exception
    */
    
   //MAC无感知认证后台进程监听 
  $Portal->listen("wifi\\protocol\\Portal", "callBackFun")
  /**
     * 后台进程  MAC无感知认证
     * @param string $callClass
     * @param string $callAction
     * @return mixed
     * @throws \Exception
     */
~~~

<hr>

## 二、目录结构

初始的目录结构如下：

~~~
wifiprotocol  目录名称（扩展类库）
├─auto          自动加载文件目录
│  │ 
│  ├─config             配置文件目录
│  │  ├─xxx.php         xxx文件
│  │  ├─xxx.php         xxx文件
│  │  └─xxx.php         xxx文件
│  ├─extra              扩展文件目录
│  │  ├─xxx.php         xxx文件
│  │  ├─xxx.php         xxx文件
│  │  └─xxx.php         xxx文件
│  ├─base.php            基数函数文件
│  ├─common.php          公共函数文件
│  ├─config.php          自定义配置文件
│  └─helper.php          自定义函数
│
├─socket           Socket协议目录
│  ├─TCP.php            TCP协议
│  └─UDP.php            UDP协议
│
├─portal           Portal协议目录
│  ├─xxx.php            xxx文件
│  └─xxx.php            xxx文件
│
├─radius           Radius协议目录
│  ├─xxx.php            xxx文件
│  └─xxx.php            xxx文件
│
├─library            工具类库目录
│  ├─xxx.php            xxx类库
│  └─Bytes.php          Bytes字节操作类库
│
├─extend                扩展类库目录
├─Base.php              基础类文件
├─Portal.php            Portal协议文件
├─Radius.php            Radius协议文件
├─Socket.php            Socket协议文件
├─LICENSE.txt           授权说明文件
├─README.md             README 文件
│


~~~

## 三、命名规范

遵循PSR-2命名规范和PSR-4自动加载规范，并且注意如下规范：

### 目录和文件

*   目录不强制规范，驼峰和小写+下划线模式均支持；
*   类库、函数文件统一以`.php`为后缀；
*   类的文件名均以命名空间定义，并且命名空间的路径和类库文件所在路径一致；
*   类名和类文件名保持一致，统一采用驼峰法命名（首字母大写）；

### 函数和类、属性命名

*   类的命名采用驼峰法，并且首字母大写，例如 `User`、`UserType`，默认不需要添加后缀，例如`UserController`应该直接命名为`User`；
*   函数的命名使用小写字母和下划线（小写字母开头）的方式，例如 `get_client_ip`；
*   方法的命名使用驼峰法，并且首字母小写，例如 `getUserName`；
*   属性的命名使用驼峰法，并且首字母小写，例如 `tableName`、`instance`；
*   以双下划线“__”打头的函数或方法作为魔法方法，例如 `__call` 和 `__autoload`；


## 四、参与开发

请参阅 [AAAA-WIFI 核心框架包](https://github.com/dtypt-L/wifiprotocol)。

## 五、版权信息

AAAA-WIFI遵循Apache2开源协议发布，并提供免费使用。

本项目包含的第三方源码和二进制文件之版权信息另行标注。

版权所有Copyright © 2019-2020 by AAAA-WIFI DTYPT All rights reserved。

AAAA-WIFI® DTYPT(@all)。

更多细节参阅 [LICENSE.txt](LICENSE.txt)
